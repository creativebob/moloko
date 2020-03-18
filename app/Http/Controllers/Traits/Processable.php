<?php

namespace App\Http\Controllers\Traits;

use App\Http\Controllers\Traits\Photable;
use App\Process;
use App\ProcessesGroup;
use App\Entity;
use App\Unit;

// Валидация
use App\Http\Requests\ProcessRequest;

use Illuminate\Support\Facades\Log;

trait Processable
{
    use Photable;

    /**
     * Запись процесса в бд
     *
     * @param ProcessRequest $request
     * @param $category
     * @return mixed
     */
    public function storeProcess($request, $category)
    {
//        $user = $request->user();
//        $user_id = $user->id;
//        $company_id = $user->company_id;

        // TODO - 22.01.20 - При создании процесса не ищем похожую группу, а создаем новую (на Вкусняшке проблемы с дублированием имён)

        // Смотрим пришедший режим группы процессов
        switch ($request->mode) {

            case 'mode-default':
                //            $processes_group = ProcessesGroup::firstOrCreate([
                //                'name' => $request->name,
                //                'unit_id' => $request->unit_id,
                //                'company_id' => $company_id,
                //            ]);

                $data = $request->input();
                $processes_group = ProcessesGroup::create($data);

                // Пишем к группе связь с категорией
                $category->groups()->syncWithoutDetaching($processes_group->id);
            break;

            case 'mode-add':
//                $processes_group = ProcessesGroup::firstOrCreate([
//                    'name' => $request->group_name,
//                    'unit_id' => $request->unit_id,
//                    'company_id' => $company_id,
//                ]);

                $data = $request->input();
                $data['name'] = $request->group_name;
                $processes_group = ProcessesGroup::create($data);

                // Пишем к группе связь с категорией
                $category->groups()->syncWithoutDetaching($processes_group->id);
            break;

            case 'mode-select':
                $processes_group = ProcessesGroup::findOrFail($request->group_id);
            break;
        }

        Log::channel('operations')
            ->info('Режим создания: ' . $request->mode . '. Записали или нашли группу процессов c id: ' . $processes_group->id . ', в зависимости от режима. Связали с категорией.');

        $data = $request->input();
        $data['processes_group_id'] = $processes_group->id;
        $data['processes_type_id'] = $category->processes_type_id;

        if (isset($data['units_category_id'])) {

            // Смотрим статичную категорию id 3 (Время), если пришла она по переводим к выбранному коэффициенту
            if($data['units_category_id'] == 3) {

                $unit = Unit::findOrFail($data['unit_id']);
                $length = $unit->ratio;
                $data['length'] = $length;

//            } elseif ($data['units_category_id'] == 5) {
//
//                $unit = Unit::findOrFail($data['unit_id']);
//                $volume = $unit->ratio;
//                $data['volume'] = $volume;

            } else {

                // Если нет, то умножаем пришедший вес на количество чего либо
                // $extra_unit = Unit::findOrFail($data['extra_unit_id']);


                // Если не пришло кол-во веса, значит у пользователя его не запросили, так как планируеться измерять
                // в единицах веса. Установим единицу!

//                if(isset($data['weight'])){
//                    $weight_unit = Unit::findOrFail($data['unit_weight_id']);
//                    $weight = $data['weight'] * $weight_unit->ratio;
//                    $data['weight'] = $weight;
//                };
//
//                if(isset($data['volume'])){
//                    $volume_unit = Unit::findOrFail($data['unit_volume_id']);
//                    $volume = $data['volume'] * $volume_unit->ratio;
//                    $data['volume'] = $volume;
//                };
            }
        }
        // dd($length);

        $process = Process::create($data);
        Log::channel('operations')
            ->info('Записали процесс c id: ' . $process->id);

        return $process;
    }

    /**
     * Изменение процесса в бд
     *
     * @param $request
     * @param $item
     * @return mixed
     */
    public function updateProcess($request, $item)
    {

        $process = $item->process;
        // dd($process);

        // Получаем пришедшие данные
        $data = $request->input();
        $data['old_draft'] = $process->draft;
        // dd($data);

        // Проверка только если статус черновика не пришел, а сам артикул находится в черновике
        if ((request()->draft == 0) && $data['old_draft'] == 1) {
            // Проверяем совпадение (отдаем пришедшие данные, т.к. мы не можем сейчас записать артикул, запись будет после проверки)
            // Придет либо массив с ошибками, либо null
            $result = $this->checkCoincidenceProcess($data);
            // dd($result);
        }
        // Проверки уже выведенного артикула

        // Если ошибок нет, то обновляем состав и сам артикул
        if (empty($result)) {

            $result = $this->checks($request, $item);

            if (is_array($result)) {
                return $result;
            } else {

                if ($process->draft) {
                    // Обновляем составы только для услуг в черноике
                    if ($item->getTable() == 'services') {

                        if($process->kit) {
                            $access = session('access.all_rights.index-services-allow');
                            if ($access) {
                                $process->services()->sync($request->services);
                            }
                        } else {
                            $access = session('access.all_rights.index-workflows-allow');
                            if ($access) {
                                $process->workflows()->sync($request->workflows);
                            }
                        }
                    }
                }

                $access = session('access.all_rights.index-positions-allow');
                if ($access) {
                    $process->positions()->sync($request->positions);
                }

                 if ($request->has('unit_length_id')) {
                     $unit = Unit::findOrFail($request->unit_length_id);
                     $length = $data['length'] * $unit->ratio;
                     $data['length'] = $length;
                 }

                $data['draft'] = $request->draft;

                $photo_id = $this->getPhotoId($request, $process);
                $data['photo_id'] = $photo_id;

                // Если ошибок и совпадений нет, то обновляем процесс
                $process->update($data);

                return $process;
            }
        } else {
            // Если были ошибки, отдаем массив с ошибками
            return $result;
        }
    }

    /**
     * Дублирование процесса
     *
     * @param $request
     * @param $item
     * @return mixed
     */
    public function replicateProcess($request, $item)
    {

        $process = $item->process;
        $new_process = $process->replicate();
        $new_process->name = $request->name;
        $new_process->draft = true;


        if ($request->cur_group == 0) {
            $group = $process->group;

            $data = $request->input();
            $data['unit_id'] = $group->unit_id;
            $data['units_category_id'] = $group->units_category_id;
            $processes_group = ProcessesGroup::create($data);

            // TODO - 23.09.19 - Изменения из за проблен на Вкусняшке
//            $user = $request->user();
//            $processes_group = ProcessesGroup::firstOrCreate([
//                'name' => $request->name,
//                'unit_id' => $group->unit_id,
//                'units_category_id' => $group->units_category_id,
//                'company_id' => $user->company_id,
//            ]);
            $new_process->processes_group_id = $processes_group->id;

            $category = $item->category;
            $category->groups()->syncWithoutDetaching($processes_group->id);
        }

        $new_process->photo_id = null;
        $new_process->album_id = null;

        $new_process->save();

        if ($new_process) {

            $photo_id = $this->replicatePhoto($process, $new_process);
            $new_process->photo_id = $photo_id;
//
            $album_id = $this->replicateAlbumWithPhotos($process, $new_process);
            $new_process->album_id = $album_id;

            $new_process->save();

            return $new_process;
        }
    }

    /**
     * Поиск процесса
     *
     * @param $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($search)
    {

        // Подключение политики
//        $this->authorize('index',  $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));

//        $search = $request->search;
        $items = $this->class::with([
            'process'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->whereHas('process', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            })
            ->where('archive', false)
            ->get([
                'id',
                'process_id'
            ]);

//        dd($items);

        return response()->json($items);
    }

    /**
     * Проверяем процесс при выводе из черновика
     *
     * @param $data
     * @return mixed
     */
    protected function checkCoincidenceProcess($data)
    {
//         dd($data);

        $processes = Process::where([
            'processes_group_id' => $data['processes_group_id'],
            'manufacturer_id' => $data['manufacturer_id'],
        ])
        ->where('draft', false)
        ->where('id', '!=', $data['id'])
        ->get([
            'id',
            'name',
            'processes_group_id',
            'manufacturer_id',
        ]);
        // dd($processes);

        // Если нашлись артикулы
        if ($processes->isNotEmpty()) {
            // Проверяем на наличие состава

            // Формируем массив пришедших радочих процессов для процесса
            if (isset($data['workflows'])) {
                $process_workflows = [];
                foreach ($data['workflows'] as $id => $wokrflow) {
                    $process_workflows[$id] = (int) $wokrflow['value'];
                }
                // ksort($process_workflows);
                // dd($process_workflows);
                $processes = $processes->load('workflows');
            }

            // Проверяем значения составов
            foreach ($processes as $compared_process) {

                if (isset($data['workflows'])) {
                    if ($compared_process->workflows->isNotEmpty()) {
                        // Берем составы для первого найдденного артикула в группе
                        $compared_process_workflows = [];
                        foreach ($compared_process->workflows as $wokrflow) {
                            $compared_process_workflows[$wokrflow->id] = $wokrflow->pivot->value;
                        }
                        // ksort($compared_process_workflows);
                        // dd($compared_process_workflows);
                    }
                }

                // Если составы и их значения совпали, то так как один производитель, даем ошибку
                if (isset($process_workflows) && isset($compared_process_workflows)) {
                    // dd('lol1');
                    if ($process_workflows == $compared_process_workflows) {
                        $result['msg'] = 'В данной групе существует процесс с таким составом и производителем.';
                        return $result;
                    } else {
                        // Если имя совпало даем ошибку
                        if ($data['name'] == $compared_process->name) {
                            $result['msg'] = 'В данной групе существует процесс с таким именем.';
                            return $result;
                        }
                    }
                } else {
                    // Если составы разные, смотрим имя, так как производитель один

                    // Если имя совпало даем ошибку
                    if ($data['name'] == $compared_process->name) {
                        $result['msg'] = 'В данной групе существует процесс с таким именем.';
                        return $result;
                    }

                    // Убиваем массив, чтоб создать новый
                    unset($compared_process_workflows);
                }
            }
            // dd('lol');
        }
    }

    /**
     * Проверки уже выведенного процесса
     *
     * @param $request
     * @param $item
     * @return mixed
     */
    protected function checks($request, $item)
    {

        $process = $item->process;

        // Проверка имени
        if ($process->name != $request->name) {

            $result = $this->checkName($request, $item);
            if (is_array($result)) {
                return $result;
            }
        }

        // Проверка смены группы
        if ($process->processes_group_id != $request->processes_group_id) {
            $data = $request->input();
            // Так как производителя блокируем на шаблоне, то добавляем руками в массив
            $data['manufacturer_id'] = $item->process->manufacturer_id;

            // Если это товар и не шаблон, то вытаскиваем его состав для сравнения с артикулами из новой группы
            if ($item->getTable() == 'services' && !$process->draft) {
                $process = $process->load('workflows');

                // ПРиводим массив к виду с шаблона
                $workflows = [];
                foreach ($process->workflows as $wokrflow) {
                    $workflows[$wokrflow->pivot->workflow_id]['value'] = $wokrflow->pivot->value;
                }
                // ksort($workflows);
                $data['workflows'] = $workflows;
            }

            $result = $this->checkCoincidenceProcess($data);

            if (is_array($result)) {
                return $result;
            }
        }
    }

    /**
     * Проверяем на совпадение имени процесса (не черновика)
     *
     * @param $request
     * @param $item
     * @return mixed
     */
    protected function checkName($request, $item)
    {
        if (!$item->process->draft) {
            $process = $item->process;
            $processes_count = Process::where([
                'name' => $request->name,
                'processes_group_id' => $process->processes_group_id,
                'manufacturer_id' => $process->manufacturer_id,
            ])
            ->count();

            if ($processes_count > 0) {
                $result['msg'] = 'В данной групе существует процесс с таким именем.';
                return $result;
            }
        }
    }

    /**
     * Смена категории
     *
     * @param $request
     * @param $item
     */
    protected function changeCategory($request, $item)
    {

        // Получаем выбранную категорию со страницы (то, что указал пользователь)
        $category_id = $request->category_id;

            // Смотрим: была ли она изменена
        if ($item->category_id != $category_id) {

            $processes_group = $item->process->group;
            $category = $item->category;

            // Была изменена! Переназначаем категорию товару и группе:
            $category->groups()->detach($processes_group->id);


            $entity = Entity::where('alias', $item->category->getTable())
                ->first(['model']);
            $model = 'App\\'.$entity->model;

            $new_category = $model::findOrFail($category_id);
            $new_category->groups()->attach($request->processes_group_id);

            $item->update([
                'category_id' => $category_id,
            ]);
        }
    }
}
