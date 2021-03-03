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

class ServiceController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * ServiceController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'services';
        $this->entityDependence = false;
        $this->class = Service::class;
    }

    use Processable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Service::class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entityAlias);
        if (($filter_url != null) && ($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entityAlias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $services = Service::with([
            'author',
            'company',
            'process' => function ($q) {
                $q->with([
                    'group',
                    'photo',
                    'positions',
                    'impacts'
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
//        ->filter($request, 'author_id')
            // ->filter($request, 'services_category_id', 'process.product')
            // ->filter($request, 'services_product_id', 'process')

            ->filter()

            ->where('archive', false)
//        ->select($columns)
            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);
        // dd($services);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'author',               // Автор записи
            // 'services_category',    // Категория услуги
            // 'services_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('products.processes.common.index.index', [
            'items' => $services,
            'pageInfo' => $pageInfo,
            'class' => Service::class,
            'entity' => $this->entityAlias,
            'category_entity' => 'services_categories',
            'filter' => $filter,
        ]);
    }

    /**
     * Отображение архивных записей
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archives(Request $request)
    {

        // Подключение политики
        $this->authorize('index', Service::class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entityAlias);

        if (($filter_url != null) && ($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entityAlias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

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
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->booklistFilter($request)
//            ->filter($request, 'author_id')
//
//            ->whereHas('article', function($q) use ($request){
//                $q->filter($request, 'articles_group_id');
//            })
//
//            ->filter($request, 'category_id')
            // ->filter($request, 'goods_product_id', 'article')
            ->where('archive', true)
//        ->select($columns)
            ->orderBy('moderation', 'desc')
            ->oldest('id')
            ->paginate(30);
//         dd($goods);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'author',               // Автор записи
//            'goods_category',       // Категория товара
//            'articles_group',    // Группа артикула
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('products.processes.common.index.index', [
            'items' => $services,
            'pageInfo' => $pageInfo,
            'class' => Service::class,
            'entity' => $this->entityAlias,
            'category_entity' => 'services_categories',
            'filter' => $filter,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Service::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, 'index');

        // Главный запрос
        $servicesCategories = ServicesCategory::withCount('manufacturers')
            ->with('manufacturers')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            ->orderBy('sort', 'asc')
            ->get();

        if ($servicesCategories->count() == 0) {

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
            'item' => Service::make(),
            'title' => 'Добавление услуги',
            'entity' => $this->entityAlias,
            'category_entity' => 'services_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ServiceStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Service::class);

        logs('operations')
            ->info('========================================== НАЧИНАЕМ ЗАПИСЬ УСЛУГИ ==============================================');

        $servicesCategory = ServicesCategory::find($request->category_id);
        // dd($servicesCategory->load('groups'));
        $process = $this->storeProcess($request, $servicesCategory);

        if ($process) {

            $data = $request->input();
            $data['process_id'] = $process->id;
            $service = Service::create($data);

            if ($service) {

//                $servicesCategory = $servicesCategory->load('workflows:id');
                $workflows = $servicesCategory->workflows->pluck('id')->toArray();
                $process->workflows()->sync($workflows);

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);
                logs('operations')
                    ->info('Записали услугу с id: ' . $service->id);
                logs('operations')
                    ->info('Автор: ' . $service->author->name . ' id: ' . $service->author_id . ', компания: ' . is_null($service->company) ? 'шаблон' : $service->company->name . ', id: ' . $service->company_id);
                logs('operations')
                    ->info('========================================== КОНЕЦ ЗАПИСИ УСЛУГИ ==============================================

                    ');
                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('services.index');
                } else {
                    return redirect()->route('services.edit', $service->id);
                }
            } else {
                abort(403, __('errors.store'));
            }
        } else {
            abort(403, 'Ошибка записи информации услуги');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // Главный запрос
        $service = Service::moderatorLimit($answer)
            ->find($id);
        //        dd($service);
        if (empty($service)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        $service->load([
            'process' => function ($q) {
                $q->with([
                    'unit',
                    'workflows' => function ($q) {
                        $q->with([
                            'category',
                            'process.unit'
                        ]);
                    },
                    'services' => function ($q) {
                        $q->with([
                            'category',
                            'process.unit'
                        ]);
                    },
                    'impacts' => function ($q) {
                        $q->with([
                            'category',
                            'article.unit'
                        ]);
                    },
                    'positions',
                ]);
            },
            'metrics',
            'prices' => function ($q) {
                $q->with([
                    'catalog',
                    'catalogs_item.parent.parent',
                    'filial',
                    'currency'
                ]);
            },
        ]);
//        dd($service);

        $process = $service->process;
        // dd($process);

        // Получаем настройки по умолчанию
        $settings = $this->getPhotoSettings($this->entityAlias);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);
        // dd($pageInfo);

        return view('products.processes.common.edit.edit', [
            'title' => 'Редактировать услугу',
            'item' => $service,
            'process' => $process,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entityAlias,
            'category_entity' => 'services_categories',
            'categories_select_name' => 'services_category_id',
            'previous_url' => url()->previous()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ServiceUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $service = Service::moderatorLimit($answer)
            ->find($id);
        //        dd($service);
        if (empty($service)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        $process = $service->process;
        // dd($process);

        $result = $this->updateProcess($request, $service);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $service);

            $data = $request->input();
            $service->update($data);

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

            if ($service->archive) {
                return redirect()->route('services.archives');
            } else {
                return redirect()->route('services.index');
            }
        } else {
            return back()
                ->withErrors($result)
                ->withInput();
        }
    }

    /**
     * Архивация указанного ресурса.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archive(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('destroy'));

        // ГЛАВНЫЙ ЗАПРОС:
        $service = Service::moderatorLimit($answer)
            ->find($id);
        //        dd($service);
        if (empty($service)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $service);

        $service->archive = true;

        // Скрываем бога
        $service->editor_id = hideGod($request->user());
        $service->save();

        if ($service) {
            return redirect()->route('services.index');
        } else {
            abort(403, __('errors.archive'));
        }

    }

    public function replicate(Request $request, $id)
    {
        $service = Service::find($id);

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

        if ($process->kit) {
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
