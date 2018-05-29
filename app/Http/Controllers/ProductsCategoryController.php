<?php

namespace App\Http\Controllers;

// Модели
use App\ProductsCategory;
use App\ProductsType;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\ProductsCategoryRequest;

// Политика
use App\Policies\ProductsCategoryPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

class ProductsCategoryController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'products_categories';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ProductsCategory::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        $products_categories = ProductsCategory::with('products_type')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем массив с вложенными элементами дял отображения дерева с правами, отдаем обьекты сущности и авторизованного пользователя
        $products_categories_tree = get_index_tree_with_rights($products_categories, $user);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('products_categories.category-list', ['products_categories_tree' => $products_categories_tree, 'id' => $request->id]);
        }

        return view('products_categories.index', compact('products_categories_tree', 'page_info'));
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ProductsCategory::class);

        $products_category = new ProductsCategory;

        // Выбираем все типы без проверки, так как они статичны, добавляться не будут
        $products_types_list = ProductsType::get()->pluck('name', 'id');

        // Если добавляем вложенный элемент
        if (isset($request->parent_id)) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $products_categories = ProductsCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->orderBy('sort', 'asc')
            ->get(['id','name','category_status','parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $products_categories_list = get_select_tree($products_categories, $request->parent_id, null, null);
            // echo $products_categories_list;

            return view('products_categories.create-medium', ['products_category' => $products_category, 'products_categories_list' => $products_categories_list, 'products_types_list' => $products_types_list]);
        } else {

            return view('products_categories.create-first', ['products_category' => $products_category, 'products_types_list' => $products_types_list]);
        }
    }

    public function store(ProductsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ProductsCategory::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем в базу
        $products_category = new ProductsCategory;
        $products_category->company_id = $company_id;
        $products_category->author_id = $user_id;
        $products_category->products_type_id = $request->products_type_id;

        // Модерация и системная запись
        $products_category->system_item = $request->system_item;
        
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $products_category->moderation = 1;
        }

        // Смотрим что пришло
        // Если категория
        if ($request->first_item == 1) {
            $products_category->category_status = 1;
        }

        // Если категория альбомов
        if ($request->medium_item == 1) {
            $products_category->parent_id = $request->parent_id;
        }

        // Делаем заглавной первую букву
        $products_category->name = get_first_letter($request->name);

        $products_category->save();

        if ($products_category) {

            // Переадресовываем на index
            return redirect()->action('ProductsCategoryController@index', ['id' => $products_category->id]);
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
        $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $products_category = ProductsCategory::with('products_type')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $products_category);

        // Выбираем все типы без проверки, так как они статичны, добавляться не будут
        $products_types_list = ProductsType::get()->pluck('name', 'id');

        if ($products_category->category_status == 1) {

            // Меняем категорию
            return view('products_categories.edit-first', ['products_category' => $products_category, 'products_types_list' => $products_types_list]);
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $products_categories = ProductsCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->orderBy('sort', 'asc')
            ->get(['id','name','category_status','parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $products_categories_list = get_select_tree($products_categories, $products_category->parent_id, null, $products_category->id);

            return view('products_categories.edit-medium', ['products_category' => $products_category, 'products_categories_list' => $products_categories_list, 'products_types_list' => $products_types_list]);
        }
    }

    public function update(ProductsCategoryRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $products_category = ProductsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $products_category);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Модерация и системная запись
        $products_category->system_item = $request->system_item;
        $products_category->moderation = $request->moderation;

        $products_category->parent_id = $request->parent_id;
        $products_category->editor_id = $user_id;
        $products_category->products_type_id = $request->products_type_id;

        // Делаем заглавной первую букву
        $products_category->name = get_first_letter($request->name); 

        $products_category->save();

        if ($products_category) {

            // Переадресовываем на index
            return redirect()->action('ProductsCategoryController@index', ['id' => $products_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи сектора!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $products_category = ProductsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $products_category);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $products_category_parent = ProductsCategory::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Если содержит, то даем сообщение об ошибке
        if ($products_category_parent) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
            ];
        } else {

            // Если нет, мягко удаляем
            if ($products_category->category_status == 1) {
                $parent = null;
            } else {
                $parent = $products_category->parent_id;
            }

            $products_category->editor_id = $user_id;
            $products_category->save();

            $products_category = ProductsCategory::destroy($id);

            if ($products_category) {

                // Переадресовываем на index
                return redirect()->action('ProductsCategoryController@index', ['id' => $parent]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при записи сектора!'
                ];
            }
        }
    }

    // Проверка наличия в базе
    public function products_category_check(Request $request)
    {
        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка отдела в нашей базе данных
        $products_category = ProductsCategory::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($products_category) {
            $result = [
                'error_status' => 1,
            ];

        // Если нет
        } else {
            $result = [
                'error_status' => 0,
            ];
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Список категорий альбомов
    public function products_category_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $products_categories = ProductsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($products_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $products_categories_list = get_select_tree($products_categories, $request->parent, null, $request->id);
        // dd($products_categories_list);

        // Отдаем ajax
        echo json_encode($products_categories_list, JSON_UNESCAPED_UNICODE);
    }

    // Сортировка
    public function products_categories_sort(Request $request)
    {

        $result = '';
        $i = 1;

        foreach ($request->products_categories as $item) {

            $products_category = ProductsCategory::findOrFail($item);
            $products_category->sort = $i;
            $products_category->save();
            $i++;
        }
    }
}
