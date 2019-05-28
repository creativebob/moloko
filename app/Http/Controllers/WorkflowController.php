<?php

namespace App\Http\Controllers;

// Модели
use App\Workflow;
use App\WorkflowsCategory;
use App\Process;
use App\Manufacturer;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\WorkflowRequest;
use App\Http\Requests\ProcessRequest;

// Куки
use Illuminate\Support\Facades\Cookie;

// Трейты
use App\Http\Controllers\Traits\Processes\ProcessTrait;

class WorkflowController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Workflow $workflow)
    {
        $this->middleware('auth');
        $this->workflow = $workflow;
        $this->class = Workflow::class;
        $this->model = 'App\Workflow';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use ProcessTrait;

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
            'set_status',
            'author_id',
            'company_id',
            'display',
            'system_item'
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
            'category' => function ($q) {
                $q->select([
                    'id',
                    'name'
                ]);
            },
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
        ->select($columns)
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
        $page_info = pageInfo($this->entity_alias);

        return view('products.processes.common.index.index', [
            'items' => $workflows,
            'page_info' => $page_info,
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
        ]);
    }

    public function store(ProcessRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $workflows_category = WorkflowsCategory::findOrFail($request->category_id);
        // dd($workflows_category->load('groups'));
        $process = $this->storeProcess($request, $workflows_category);

        if ($process) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            $workflow = new Workflow;
            $workflow->process_id = $process->id;
            $workflow->category_id = $request->category_id;

            $workflow->display = $request->display;
            $workflow->system_item = $request->system_item;

            $workflow->set_status = $request->has('set_status');

            $workflow->company_id = $user->company_id;
            $workflow->author_id = hideGod($user);
            $workflow->save();

            if ($workflow) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('workflows.index');
                } else {
                    return redirect()->route('workflows.edit', ['id' => $workflow->id]);
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
        ->findOrFail($id);
        // dd($workflow);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflow);

        $process = $workflow->process;
        // dd($process);

        // Получаем настройки по умолчанию
        $settings = getSettings($this->entity_alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('products.processes.common.edit.edit', [
            'title' => 'Редактировать рабочий процесс',
            'item' => $workflow,
            'process' => $process,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
            'category_entity' => 'workflows_categories',
            'categories_select_name' => 'workflows_category_id',
        ]);
    }

    public function update(ProcessRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $workflow = Workflow::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($workflow);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflow);

        $process = $workflow->process;
        // dd($process);

        $result = $this->updateProcess($request, $workflow);
        // Если результат не массив с ошибками, значит все прошло удачно
        if (!is_array($result)) {

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $workflow);

            $workflow->display = $request->display;
            $workflow->system_item = $request->system_item;
            $workflow->save();


            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
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
        ->findOrFail($id);

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

    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_workflow(Request $request)
    {
        $workflow = Workflow::with([
            'process.group.unit',
            'category'
        ])
        ->find($request->id);

        return view('services.workflows.workflow_input', compact('workflow'));
    }

    // Добавляем состав
    public function ajax_get_category_workflow(Request $request)
    {

        $workflow = Workflow::with([
            'process.group.unit',
            'category'
        ])
        ->findOrFail($request->id);

        return view('services_categories.workflows.workflow_tr', compact('workflow'));
    }
}
