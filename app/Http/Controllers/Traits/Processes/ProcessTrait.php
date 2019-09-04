<?php

namespace App\Http\Controllers\Traits\Processes;

use App\Http\Controllers\Traits\Photable;
use App\Process;
use App\ProcessesGroup;
use App\Entity;
use App\Unit;

// Валидация
use App\Http\Requests\ProcessRequest;

use Illuminate\Support\Facades\Log;

trait ProcessTrait
{

    use Photable;

    public function storeProcess(ProcessRequest $request, $category)
    {

        $user = $request->user();
        $user_id = $user->id;
        $company_id = $user->company_id;

        // Смотрим пришедший режим группы товаров
        switch ($request->mode) {

            case 'mode-default':
            $processes_group = ProcessesGroup::firstOrCreate([
                'name' => $request->name,
                'unit_id' => $request->unit_id,
                'company_id' => $company_id,
            ]);

            // Пишем к группе связь с категорией
            $category->groups()->syncWithoutDetaching($processes_group->id);

            break;

            case 'mode-add':
            $processes_group = ProcessesGroup::firstOrCreate([
                'name' => $request->group_name,
                'unit_id' => $request->unit_id,
                'company_id' => $company_id,
            ]);

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

        // Смотрим статичную категорию id 3 (Время), если пришла она по переводим к выбранному коэффициенту
        if ($data['units_category_id'] == 3) {
            $unit = Unit::findOrFail($data['unit_id']);
            $length = $unit->ratio;
            $data['unit_id'] = null;
        } else {
            // Если нет, то умножаем пришедшую продолжительность на количество чего либо
            $extra_unit = Unit::findOrFail($data['extra_unit_id']);
            $length = $data['length'] * $extra_unit->ratio;
            $data['unit_id'] = $data['extra_unit_id'];
        }
        // dd($length);
        $data['length'] = $length;

        $process = (new Process())->create($data);
        Log::channel('operations')
            ->info('Записали процесс c id: ' . $process->id);

        return $process;
    }


    public function updateProcess(ProcessRequest $request, $item)
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
                            $this->setServices($request, $process);
                        } else {
                            $this->setWorkflows($request, $process);
                        }
                    }

                    if (isset($process->unit_id)) {
                        $unit = Unit::findOrFail($process->unit_id);
                        $length = $data['length'] * $unit->ratio;
                        $data['length'] = $length;
                    }
                }

                $data['draft'] = $request->draft;

                $data['photo_id'] = $this->getPhotoId($request, $process);

                // Если ошибок и совпадений нет, то обновляем артикул
                $process->update($data);

                return $process;
            }
        } else {
            // Если были ошибки, отдаем массив с ошибками
            return $result;
        }
    }

    protected function setWorkflows($request, $process)
    {
        // Запись состава только для черновика
        if ($process->draft) {
            $process->workflows()->sync($request->workflows);
        }
    }

    protected function setServices($request, $process)
    {
        // Запись состава только для черновика
        if ($process->draft) {
            $process->services()->sync($request->services);
        }
    }

    // Проверяем артикул при выводе из черновика
    protected function checkCoincidenceProcess($data)
    {

        // dd($data);

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

    // Проверки уже выведенного артикула
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
                    $workflows[$wokrflow_id]['value'] = $wokrflow->pivot->value;
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

    // Проверяем на совпадение имя артикула (не черновика)
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

    // Проверяем на совпадение имя артикула (не черновика)
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
            $new_category->groups()->attach($request->articles_group_id);

            $item->update([
                'category_id' => $category_id,
            ]);
        }
    }
}