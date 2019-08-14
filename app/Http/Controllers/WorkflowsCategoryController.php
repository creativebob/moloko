<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkflowsCategoryUpdateRequest;
use App\Http\Requests\WorkflowsCategoryStoreRequest;
use App\WorkflowsCategory;
use Illuminate\Http\Request;

class WorkflowsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(WorkflowsCategory $workflows_category)
    {
        $this->middleware('auth');
        $this->workflows_category = $workflows_category;
        $this->class = WorkflowsCategory::class;
        $this->model = 'App\WorkflowsCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $workflows_categories = WorkflowsCategory::with([
            'workflows',
            'childs',
            'groups'
        ])
        ->withCount('childs')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        // ->withCount('products')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('common.accordions.categories_list',
                [
                    'items' => $workflows_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $workflows_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'workflows_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('common.accordions.index',
            [
                'items' => $workflows_categories,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'childs_count',
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist'
                ]),
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории рабочих процессов',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(WorkflowsCategoryStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $workflows_category = (new $this->class())->create($data);

        if ($workflows_category) {
            // Переадресовываем на index
            return redirect()->route('workflows_categories.index', ['id' => $workflows_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории сырья!',
            ];
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

        // ГЛАВНЫЙ ЗАПРОС:
        $workflows_category = WorkflowsCategory::with([
            'manufacturers',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflows_category);
        // dd($workflows_category_metrics);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getSettings($this->entity_alias);

        // dd($goods_category->direction);
        return view('products.processes_categories.common.edit.edit', [
            'title' => 'Редактирование категории рабочих процессов',
            'category' => $workflows_category,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    public function update(WorkflowsCategoryUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $workflows_category = WorkflowsCategory::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflows_category);

        $data = $request->input();
        $result = $workflows_category->update($data);

        if ($result) {

            // Производители
            $workflows_category->manufacturers()->sync($request->manufacturers);

           // Переадресовываем на index
            return redirect()->route('workflows_categories.index', ['id' => $workflows_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении категории рабочих процессов!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $workflows_category = WorkflowsCategory::with([
            'childs',
            'workflows'
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $workflows_category);

        // Скрываем бога
        $workflows_category->editor_id = hideGod($request->user());
        $workflows_category->save();

        $parent_id = $workflows_category->parent_id;

        $workflows_category->delete();

        if ($workflows_category) {

                // Переадресовываем на index
            return redirect()->route('workflows_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}
