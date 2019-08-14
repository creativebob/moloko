<?php

namespace App\Http\Controllers;

// Модели
use App\ExpendablesCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ExpendablesCategoryRequest;



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

            return view('common.accordions.categories_list',
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
        return view('common.accordions.index',
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

        return view('common.accordions.create', [
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

        // Заполнение и проверка основных полей в трейте
        $expendables_category = $this->storeCategory($request);

        $expendables_category->save();

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

    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $expendables_category = ExpendablesCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $expendables_category);

        return view('common.accordions.edit', [
            'item' => $expendables_category,
            'entity' => $this->entity_alias,
            'title' => 'Редактирование сектора',
            'parent_id' => $expendables_category->parent_id,
            'category_id' => $expendables_category->category_id
        ]);
    }

    public function update(ExpendablesCategoryRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $expendables_category = ExpendablesCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $expendables_category);

        // Заполнение и проверка основных полей в трейте
        $expendables_category = $this->updateCategory($expendables_category);

        $expendables_category->save();

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

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $expendables_category = ExpendablesCategory::with('childs')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $expendables_category);

        // Скрываем бога
        $expendables_category->editor_id = hideGod($request->user());
        $expendables_category->save();

        $parent_id = $expendables_category->parent_id;

        $expendables_category = ExpendablesCategory::destroy($id);

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
