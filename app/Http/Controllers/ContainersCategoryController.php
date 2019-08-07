<?php

namespace App\Http\Controllers;

use App\ContainersCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ContainersCategoryRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

class ContainersCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(ContainersCategory $containers_category)
    {
        $this->middleware('auth');
        $this->containers_category = $containers_category;
        $this->class = ContainersCategory::class;
        $this->model = 'App\ContainersCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $containers_categories = ContainersCategory::with([
            'containers',
            'childs',
            'groups'
        ])
            ->withCount('childs')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('common.accordions.categories_list',
                [
                    'items' => $containers_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $containers_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'containers_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('common.accordions.index',
            [
                'items' => $containers_categories,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'childs_count',
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist',
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
            'title' => 'Добавление категории упаковок',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(ContainersCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $containers_category = $this->storeCategory($request);

        $containers_category->save();

        if ($containers_category) {
            // Переадресовываем на index
            return redirect()->route('containers_categories.index', ['id' => $containers_category->id]);
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
        $containers_category = ContainersCategory::with([
            'manufacturers',
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);
        // dd($containers_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $containers_category);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getSettings($this->entity_alias);

        return view('products.articles_categories.common.edit.edit', [
            'title' => 'Редактирование категории упаковок',
            'category' => $containers_category,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    public function update(ContainersCategoryRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $containers_category = ContainersCategory::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $containers_category);

        // Заполнение и проверка основных полей в трейте
        $containers_category = $this->updateCategory($request, $containers_category);

        $containers_category->save();

        if ($containers_category) {

            // Производители
            $containers_category->manufacturers()->sync($request->manufacturers);

            // Переадресовываем на index
            return redirect()->route('containers_categories.index', ['id' => $containers_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении категории сырья!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $containers_category = ContainersCategory::with([
            'childs',
            'containers'
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $containers_category);

        $parent_id = $containers_category->parent_id;

        $containers_category->delete();

        if ($containers_category) {

            // Переадресовываем на index
            return redirect()->route('containers_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}