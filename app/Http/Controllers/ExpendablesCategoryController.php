<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpendablesCategoryUpdateRequest;
use App\Http\Requests\ExpendablesCategoryStoreRequest;
use App\ExpendablesCategory;
use Illuminate\Http\Request;

class ExpendablesCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(ExpendablesCategory $expendables_categories)
    {
        $this->middleware('auth');
        $this->expendables_categories = $expendables_categories;
        $this->class = ExpendablesCategory::class;
        $this->model = 'App\ExpendablesCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'modal';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $expendables_categories = ExpendablesCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        // ->withCount('companies')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.accordions.categories_list',
                [
                    'items' => $expendables_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $expendables_categories->count(),
                    'id' => $request->id,
                    'nested' => 'companies_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.accordions.index',
            [
                'items' => $expendables_categories,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'companies_count',
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

        return view('system.common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории расходников',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(ExpendablesCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $expendables_category = (new $this->class())->create($data);

        if ($expendables_category) {
            // Переадресовываем на index
            return redirect()->route('expendables_categories.index', ['id' => $expendables_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи сектора!'
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

        $expendables_category = ExpendablesCategory::with([
            'manufacturers',
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $expendables_category);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getSettings($this->entity_alias);

        // При добавлении метрики отдаем ajax новый список свойст и метрик
        if ($request->ajax()) {
            return view('products.common.metrics.properties_list', [
                'category' => $expendables_category,
                'page_info' => $page_info,
            ]);
        }

        return view('products.articles_categories.common.edit.edit', [
            'title' => 'Редактирование категории помещений',
            'category' => $expendables_category,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    public function update(ExpendablesCategoryUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $expendables_category = ExpendablesCategory::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $expendables_category);

        $data = $request->input();
        $result = $expendables_category->update($data);

        if ($result) {

            $expendables_category->manufacturers()->sync($request->manufacturers);
            $expendables_category->metrics()->sync($request->metrics);

            // Переадресовываем на index
            return redirect()->route('expendables_categories.index', ['id' => $expendables_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи сектора!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $expendables_category = ExpendablesCategory::with([
            'childs'
        ])
            ->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $expendables_category);

        $parent_id = $expendables_category->parent_id;

        $expendables_category->delete();

        if ($expendables_category) {
            // Переадресовываем на index
            return redirect()->route('expendables_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении расходников!'
            ];
        }
    }
}
