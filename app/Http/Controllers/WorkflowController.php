<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\WorkflowStoreRequest;
use App\Http\Requests\System\WorkflowUpdateRequest;
use App\Workflow;
use App\WorkflowsCategory;
use App\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Traits\Processable;
use Illuminate\Support\Facades\Log;

class WorkflowController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * WorkflowController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'workflows';
        $this->entityDependence = false;
        $this->class = Workflow::class;
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
        $this->authorize(getmethod(__FUNCTION__), Workflow::class);

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

        $workflows = Workflow::with([
            'author',
            'company',
            'compositions.service',
            'process' => function ($q) {
                $q->with([
                    'group',
                    'photo',
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
            ->systemItem($answer) // Фильтр по системным записям
            ->booklistFilter($request)
//        ->filter($request, 'author_id')
            // ->filter($request, 'workflows_category_id', 'process.product')
            // ->filter($request, 'workflows_product_id', 'process')
            ->where('archive', false)
//        ->select($columns)
            ->orderBy('moderation', 'desc')
            ->oldest('sort')
            ->paginate(30);
        // dd($workflows);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            'author',               // Автор записи
            // 'workflows_category',    // Категория услуги
            // 'workflows_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('products.processes.common.index.index', [
            'items' => $workflows,
            'pageInfo' => $pageInfo,
            'class' => Workflow::class,
            'entity' => $this->entityAlias,
            'category_entity' => 'workflows_categories',
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
        $this->authorize('index', Workflow::class);

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

        $workflows = Workflow::with([
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
//         dd($workflows);

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
            'items' => $workflows,
            'pageInfo' => $pageInfo,
            'class' => Workflow::class,
            'entity' => $this->entityAlias,
            'category_entity' => 'workflows_categories',
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
        $this->authorize(getmethod(__FUNCTION__), Workflow::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, 'index');

        // Главный запрос
        $workflowsCategories = WorkflowsCategory::withCount('manufacturers')
            ->with('manufacturers')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->orderBy('sort', 'asc')
            ->get();

        if ($workflowsCategories->count() == 0) {

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории рабочих процессов. А уже потом будем добавлять рабочие процессы. Ок?";
            $ajax_error['link'] = "/admin/workflows_categories";
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
            'item' => Workflow::make(),
            'title' => 'Добавление рабочего процесса',
            'entity' => $this->entityAlias,
            'category_entity' => 'workflows_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WorkflowStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(WorkflowStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Workflow::class);

        logs('operations')
            ->info('========================================== НАЧИНАЕМ ЗАПИСЬ РАБОЧЕГО ПРОЦЕССА ==============================================');

        $workflowsCategory = WorkflowsCategory::find($request->category_id);
        // dd($workflowsCategory->load('groups'));
        $process = $this->storeProcess($request, $workflowsCategory);

        if ($process) {

            $data = $request->input();
            $data['process_id'] = $process->id;
            $workflow = Workflow::create($data);

            if ($workflow) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                logs('operations')
                    ->info('Записали рабочий процесс с id: ' . $workflow->id);
                logs('operations')
                    ->info('Автор: ' . $workflow->author->name . ' id: ' . $workflow->author_id . ', компания: ' . $workflow->company->name . ', id: ' . $workflow->company_id);
                logs('operations')
                    ->info('========================================== КОНЕЦ ЗАПИСИ РАБОЧЕГО ПРОЦЕССА ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('workflows.index');
                } else {
                    return redirect()->route('workflows.edit', $workflow->id);
                }
            } else {
                abort(403, __('errors.store'));
            }
        } else {
            abort(403, 'Ошибка записи информации сырья');
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
        $workflow = Workflow::moderatorLimit($answer)
            ->find($id);
        // dd($workflow);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflow);

        $workflow->load([
            'process' => function ($q) {
                $q->with([
                    'unit',
                    'impacts' => function ($q) {
                        $q->with([
                            'category',
                            'article.unit'
                        ]);
                    },
                    'seo.childs.params',
                ]);
            },
            'category' => function ($q) {
                $q->with([
                    'metrics'
                ]);
            },
        ]);
//        dd($workflow);
        if (empty($workflow)) {
            abort(403, __('errors.not_found'));
        }

        $process = $workflow->process;
        // dd($process);

        // Получаем настройки по умолчанию
        $settings = $this->getPhotoSettings($this->entityAlias);
//        dd($settings);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);
        // dd($pageInfo);

        return view('products.processes.common.edit.edit', [
            'title' => 'Редактировать рабочий процесс',
            'item' => $workflow,
            'process' => $process,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entityAlias,
            'category_entity' => 'workflows_categories',
            'categories_select_name' => 'workflows_category_id',
            'previous_url' => url()->previous()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WorkflowUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(WorkflowUpdateRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $workflow = Workflow::moderatorLimit($answer)
            ->find($id);
//        dd($workflow);
        if (empty($workflow)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflow);

        $process = $workflow->process;
        // dd($process);

        $result = $this->updateProcess($request, $workflow);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $workflow);

            $data = $request->input();
            $workflow->update($data);

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
                $workflow->metrics()->syncWithoutDetaching($metrics_insert);
            }

            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            if ($request->has('previous_url')) {
                return redirect($request->previous_url);
            }

            return redirect()->route('workflows.index');
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
        $answer = operator_right($this->entityAlias, $this->entityDependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $workflow = Workflow::moderatorLimit($answer)
            ->find($id);
//        dd($workflow);
        if (empty($workflow)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $workflow);

        $workflow->archive = true;

        // Скрываем бога
        $workflow->editor_id = hideGod($request->user());
        $workflow->save();

        if ($workflow) {
            return redirect()->route('workflows.index');
        } else {
            abort(403, __('errors.archive'));
        }
    }

    public function replicate(Request $request, $id)
    {
        $workflow = Workflow::find($id);

        $workflow->load('process');
        $process = $workflow->process;
        $new_process = $this->replicateProcess($request, $workflow);

        $new_workflow = $workflow->replicate();
        $new_workflow->process_id = $new_process->id;
        $new_workflow->save();

        $workflow->load('metrics');
        if ($workflow->metrics->isNotEmpty()) {
            $metrics_insert = [];
            foreach ($workflow->metrics as $metric) {
                $metrics_insert[$metric->id]['value'] = $metric->pivot->value;
            }
            $res = $new_workflow->metrics()->attach($metrics_insert);
        }

        if ($process->kit) {
            $process->load('workflows');
            if ($process->workflows->isNotEmpty()) {
                $workflows_insert = [];
                foreach ($process->workflows as $workflow) {
                    $workflows_insert[$workflow->id]['value'] = $workflow->pivot->value;
                }
                $res = $new_process->workflows()->attach($workflows_insert);
            }
        }

        return redirect()->route('workflows.index');
    }

    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_workflow(Request $request)
    {
        $workflow = Workflow::with([
            'process.group.unit',
            'category'
        ])
            ->find($request->id);

        return view('products.processes.services.workflows.workflow_input', compact('workflow'));
    }

    // Добавляем состав
    public function ajax_get_category_workflow(Request $request)
    {

        $workflow = Workflow::with([
            'process.group.unit',
            'category'
        ])
            ->find($request->id);

        return view('products.processes_categories.services_categories.workflows.workflow_tr', compact('workflow'));
    }
}
