<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\ServiceStoreRequest;
use App\Http\Requests\System\ServiceUpdateRequest;
use App\Service;
use App\ServicesCategory;
use App\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Traits\Processable;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{

    /**
     * ServiceController constructor.
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->middleware('auth');
        $this->service = $service;
        $this->class = Service::class;
        $this->model = 'App\Service';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Processable;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $columns = [
            'id',
            'process_id',
            'category_id',
            'author_id',
            'company_id',
            'display',
            'system'
        ];

        $services = Service::with([
            'author',
            'company',
            'process' => function ($q) {
                $q->with([
                    'group',
                    'photo'
                ]);
            },
            'category'
//            => function ($q) {
//                $q->select([
//                    'id',
//                    'name'
//                ]);
//            }
            ,
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
//        ->template($answer)
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        // ->filter($request, 'services_category_id', 'process.product')
        // ->filter($request, 'services_product_id', 'process')
        ->where('archive', false)
//        ->select($columns)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($services);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            // 'services_category',    // Категория услуги
            // 'services_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);


        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('products.processes.common.index.index', [
            'items' => $services,
            'pageInfo' => $pageInfo,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'services_categories',
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('services_categories', false, 'index');

        // Главный запрос
        $services_categories = ServicesCategory::withCount('manufacturers')
        ->with('manufacturers')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('sort', 'asc')
        ->get();

        if ($services_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории услуг. А уже потом будем добавлять услги. Ок?";
            $ajax_error['link'] = "/admin/services_categories";
            $ajax_error['title_link'] = "Идем в раздел категорий";

            return view('ajax_error', compact('ajax_error'));
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('manufacturers', false, 'index');

        $manufacturers_count = Manufacturer::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->count();

        // Если нет производителей
        if ($manufacturers_count == 0) {

            // Описание ошибки
            // $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо добавить производителей. А уже потом будем добавлять рабочие процессы. Ок?";
            $ajax_error['link'] = "/admin/manufacturers/create"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел производителей"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        return view('products.processes.common.create.create', [
            'item' => new $this->class,
            'title' => 'Добавление услуги',
            'entity' => $this->entity_alias,
            'category_entity' => 'services_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    public function store(ServiceStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
        ->info('========================================== НАЧИНАЕМ ЗАПИСЬ УСЛУГИ ==============================================');

        $services_category = ServicesCategory::findOrFail($request->category_id);
        // dd($services_category->load('groups'));
        $process = $this->storeProcess($request, $services_category);

        if ($process) {

            $data = $request->input();
            $data['process_id'] = $process->id;
            $service = Service::create($data);

            if ($service) {

                $services_category = $services_category->load('workflows:id');
                $workflows = $services_category->workflows->pluck('id')->toArray();
                $process->workflows()->sync($workflows);

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);
                Log::channel('operations')
                ->info('Записали услугу с id: ' . $service->id);
                Log::channel('operations')
                ->info('Автор: ' . $service->author->name . ' id: ' . $service->author_id .  ', компания: ' . is_null($service->company) ? 'шаблон' : $service->company->name . ', id: ' . $service->company_id);
                Log::channel('operations')
                ->info('========================================== КОНЕЦ ЗАПИСИ УСЛУГИ ==============================================

                    ');
                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('services.index');
                } else {
                    return redirect()->route('services.edit', $service->id);
                }
            } else {
                abort(403, 'Ошибка записи услуги');
            }
        } else {
            abort(403, 'Ошибка записи информации услуги');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Главный запрос
        $service = Service::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($service);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        $service->load([
            'process' => function ($q) {
                $q->with([
                    'group',
                    'unit',
                    'unit_length',
                    'workflows.process.group.unit',
                    'workflows.category'
                ]);
            },
            'metrics',
            'prices'
        ]);
//        dd($service);

        $process = $service->process;
        // dd($process);

        // Получаем настройки по умолчанию
        $settings = $this->getPhotoSettings($this->entity_alias);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);
        // dd($pageInfo);

        return view('products.processes.common.edit.edit', [
            'title' => 'Редактировать услугу',
            'item' => $service,
            'process' => $process,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entity_alias,
            'category_entity' => 'services_categories',
            'categories_select_name' => 'services_category_id',
            'previous_url' => url()->previous()
        ]);
    }

    public function update(ServiceUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $service = Service::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($service);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        $process = $service->process;
        // dd($process);

        $result = $this->updateProcess($request, $service);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $service);

            $service->serial = $request->serial;
            $service->display = $request->display;
            $service->system = $request->system;
            $service->save();



            // Метрики
            if ($request->has('metrics')) {
                // dd($request);

                $metrics_insert = [];
                foreach ($request->metrics as $metric_id => $value) {
                    if (is_array($value)) {
                        $metrics_insert[$metric_id]['value'] = implode(',', $value);
                    } else {
//                        if (!is_null($value)) {
                        $metrics_insert[$metric_id]['value'] = $value;
//                        }
                    }
                }
                $service->metrics()->syncWithoutDetaching($metrics_insert);
            }

            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            return redirect()->route('services.index');
        } else {
            return back()
            ->withErrors($result)
            ->withInput();
        }
    }

    public function destroy($id)
    {
        //
    }

    public function archive(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $service = Service::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $service);

        if ($service) {

            $service->archive = true;

            // Скрываем бога
            $service->editor_id = hideGod($request->user());
            $service->save();

            if ($service) {
                return redirect()->route('services.index');
            } else {
                abort(403, 'Ошибка при архивации');
            }
        } else {
            abort(403, 'Не найдено');
        }
    }

    public function replicate(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $service->load('process');
        $process = $service->process;

        $new_process = $this->replicateProcess($request, $service);

        $new_service = $service->replicate();
        $new_service->process_id = $new_process->id;
        $new_service->save();

        $service->load('metrics');
        if ($service->metrics->isNotEmpty()) {
            $metrics_insert = [];
            foreach ($service->metrics as $metric) {
                $metrics_insert[$metric->id]['value'] = $metric->pivot->value;
            }
            $res = $new_service->metrics()->attach($metrics_insert);
        }

        $process->load('positions');
        if ($process->positions->isNotEmpty()) {
            $res = $new_process->positions()->attach($process->positions->pluck('id'));
        }

        if($process->kit) {
            $process->load('services');
            if ($process->services->isNotEmpty()) {
                $services_insert = [];
                foreach ($process->services as $service) {
                    $services_insert[$service->id]['value'] = $service->pivot->value;
                }
                $res = $new_process->services()->attach($services_insert);
            }
        } else {
            $process->load('workflows');
            if ($process->workflows->isNotEmpty()) {
                $workflows_insert = [];
                foreach ($process->workflows as $workflow) {
                    $workflows_insert[$workflow->id] = [
                        'value' => $workflow->pivot->value,
                        'useful' => $workflow->pivot->useful,
                        'waste' => $workflow->pivot->waste,
                        'leftover' => $workflow->pivot->leftover,
                        'leftover_operation_id' => $workflow->pivot->leftover_operation_id,
                    ];
                }
                $res = $new_process->workflows()->attach($workflows_insert);
            }
        }


        return redirect()->route('services.index');
    }

    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_service(Request $request)
    {
        $service = Service::with([
            'process.group.unit',
            'category'
        ])
            ->find($request->id);

        return view('products.processes.services.services.service_input', compact('service'));
    }
}
