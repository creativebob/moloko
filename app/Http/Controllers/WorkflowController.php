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

    /**
     * WorkflowController constructor.
     * @param Workflow $workflow
     */
    public function __construct(Workflow $workflow)
    {
        $this->middleware('auth');
        $this->workflow = $workflow;
        $this->class = Workflow::class;
        $this->model = 'App\Workflow';
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

        $workflows = Workflow::with([
            'author',
            'company',
            'compositions.service',
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
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        // ->filter($request, 'workflows_category_id', 'process.product')
        // ->filter($request, 'workflows_product_id', 'process')
        ->where('archive', false)
//        ->select($columns)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($workflows);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            // 'workflows_category',    // Категория услуги
            // 'workflows_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);


        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('products.processes.common.index.index', [
            'items' => $workflows,
            'pageInfo' => $pageInfo,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'workflows_categories',
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('workflows_categories', false, 'index');

        // Главный запрос
        $workflows_categories = WorkflowsCategory::withCount('manufacturers')
        ->with('manufacturers')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get();

        if($workflows_categories->count() == 0){

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
        if ($manufacturers_count == 0){

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
            'title' => 'Добавление рабочего процесса',
            'entity' => $this->entity_alias,
            'category_entity' => 'workflows_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    public function store(WorkflowStoreRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
        ->info('========================================== НАЧИНАЕМ ЗАПИСЬ РАБОЧЕГО ПРОЦЕССА ==============================================');

        $workflows_category = WorkflowsCategory::find($request->category_id);
        // dd($workflows_category->load('groups'));
        $process = $this->storeProcess($request, $workflows_category);

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

                Log::channel('operations')
                ->info('Записали рабочий процесс');
                Log::channel('operations')
                ->info('Автор: ' . $workflow->author->name . ' id: ' . $workflow->author_id .  ', компания: ' . $workflow->company->name . ', id: ' .$workflow->company_id);
                Log::channel('operations')
                ->info('========================================== КОНЕЦ ЗАПИСИ РАБОЧЕГО ПРОЦЕССА ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('workflows.index');
                } else {
                    return redirect()->route('workflows.edit', $workflow->id);
                }
            } else {
                abort(403, 'Ошибка записи сырья');
            }
        } else {
            abort(403, 'Ошибка записи информации сырья');
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
        $workflow = Workflow::moderatorLimit($answer)
        ->find($id);
        // dd($workflow);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflow);

        $workflow->load([
            'process' => function ($q) {
                $q->with([
                    'unit'
                ]);
            },
            'category' => function ($q) {
                $q->with([
                    'metrics'
                ]);
            },
        ]);
        $process = $workflow->process;
        // dd($process);

        // Получаем настройки по умолчанию
        $settings = $this->getPhotoSettings($this->entity_alias);
//        dd($settings);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);
        // dd($pageInfo);

        return view('products.processes.common.edit.edit', [
            'title' => 'Редактировать рабочий процесс',
            'item' => $workflow,
            'process' => $process,
            'pageInfo' => $pageInfo,
            'settings' => $settings,
            'entity' => $this->entity_alias,
            'category_entity' => 'workflows_categories',
            'categories_select_name' => 'workflows_category_id',
            'workflow' => $workflow,
            'previous_url' => url()->previous()
        ]);
    }

    public function update(WorkflowUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $workflow = Workflow::with('process')
        ->moderatorLimit($answer)
        ->find($id);
        // dd($workflow);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflow);

        $process = $workflow->process;
        // dd($process);

        if ($process->draft) {
            $workflow->serial = $request->serial;
        }

        $result = $this->updateProcess($request, $workflow);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $workflow);

            $workflow->display = $request->display;
            $workflow->system = $request->system;
            $workflow->save();

            $access = session('access.all_rights.index-metrics-allow');
            if ($access) {
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

    public function destroy($id)
    {
        //
    }

    public function archive(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $workflow = Workflow::with([
            'compositions.service',
        ])
        ->moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $workflow);

        if ($workflow) {

            $workflow->archive = true;

            // Скрываем бога
            $workflow->editor_id = hideGod($request->user());
            $workflow->save();

            if ($workflow) {
                return redirect()->route('workflows.index');
            } else {
                abort(403, 'Ошибка при архивации рабочего процесса');
            }
        } else {
            abort(403, 'Рабочий процесс не найден');
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

        if($process->kit) {
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
