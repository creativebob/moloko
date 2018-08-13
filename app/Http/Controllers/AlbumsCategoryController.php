<?php

namespace App\Http\Controllers;

// Модели
use App\AlbumsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\AlbumsCategoryRequest;

// Политика
use App\Policies\AlbumsCategoryPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

class AlbumsCategoryController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'albums_categories';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), AlbumsCategory::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        $albums_categories = AlbumsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны альбомов
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем массив с вложенными элементами дял отображения дерева с правами, отдаем обьекты сущности и авторизованного пользователя
        $albums_categories_tree = get_index_tree_with_rights($albums_categories, $user);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('albums_categories.category-list', ['albums_categories_tree' => $albums_categories_tree, 'id' => $request->id]);
        }

        // dd($albums_categories_tree);

        // Отдаем на шаблон
        return view('albums_categories.index', compact('albums_categories_tree', 'page_info'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), AlbumsCategory::class);

        $albums_category = new AlbumsCategory;

        // Если добавляем вложенный элемент
        if (isset($request->parent_id)) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $albums_categories = AlbumsCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->orderBy('sort', 'asc')
            ->get(['id','name','category_status','parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $albums_categories_list = get_select_tree($albums_categories, $request->parent_id, null, null);
            // echo $albums_categories_list;

            return view('albums_categories.create-medium', ['albums_category' => $albums_category, 'albums_categories_list' => $albums_categories_list]);
        } else {

            return view('albums_categories.create-first', ['albums_category' => $albums_category]);
        }
    }

    public function store(AlbumsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), AlbumsCategory::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем в базу
        $albums_category = new AlbumsCategory;
        $albums_category->company_id = $company_id;
        $albums_category->author_id = $user_id;

        // Модерация и системная запись
        $albums_category->system_item = $request->system_item;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $navigation->moderation = 1;
        }

        // Смотрим что пришло
        // Если категория
        if ($request->first_item == 1) {
            $albums_category->category_status = 1;
        }

        // Если категория альбомов
        if ($request->medium_item == 1) {
            $albums_category->parent_id = $request->parent_id;
        }

        // Отображение на сайте
        $albums_category->display = $request->display;

        // Делаем заглавной первую букву
        $albums_category->name = get_first_letter($request->name);

        $albums_category->save();

        if ($albums_category) {

            // Переадресовываем на index
            return redirect()->action('AlbumsCategoryController@index', ['id' => $albums_category->id]);
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
        $albums_category = AlbumsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albums_category);

        if ($albums_category->category_status == 1) {

            // Меняем категорию
            return view('albums_categories.edit-first', ['albums_category' => $albums_category]);
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $albums_categories = AlbumsCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->orderBy('sort', 'asc')
            ->get(['id','name','category_status','parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
            $albums_categories_list = get_select_tree($albums_categories, $albums_category->parent_id, null, $albums_category->id);

            return view('albums_categories.edit-medium', ['albums_category' => $albums_category, 'albums_categories_list' => $albums_categories_list]);
        }
    }

    public function update(AlbumsCategoryRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $albums_category = AlbumsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albums_category);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Модерация и системная запись
        $albums_category->system_item = $request->system_item;
        $albums_category->moderation = $request->moderation;

        $albums_category->parent_id = $request->parent_id;
        $albums_category->editor_id = $user_id;

        // Отображение на сайте
        $albums_category->display = $request->display;

        // Делаем заглавной первую букву
        $albums_category->name = get_first_letter($request->name);

        $albums_category->save();

        if ($albums_category) {

            // Переадресовываем на index
            return redirect()->action('AlbumsCategoryController@index', ['id' => $albums_category->id]);
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
        $albums_category = AlbumsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albums_category);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $albums_category_parent = AlbumsCategory::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Если содержит, то даем сообщение об ошибке
        if ($albums_category_parent) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
            ];
        } else {

            // Если нет, мягко удаляем
            if ($albums_category->category_status == 1) {
                $parent = null;
            } else {
                $parent = $albums_category->parent_id;
            }

            $albums_category->editor_id = $user_id;
            $albums_category->save();

            $albums_category = AlbumsCategory::destroy($id);

            if ($albums_category) {

                // Переадресовываем на index
                return redirect()->action('AlbumsCategoryController@index', ['id' => $parent]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при записи сектора!'
                ];
            }
        }
    }

    // Проверка наличия в базе
    public function albums_category_check(Request $request)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка отдела в нашей базе данных
        $albums_category = AlbumsCategory::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($albums_category) {
            $result = [
                'error_status' => 1,
            ];

            // Если нет
        } else {
            $result = [
                'error_status' => 0
            ];
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Список категорий альбомов
    public function albums_category_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $albums_categories = AlbumsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $albums_categories_list = get_select_tree($albums_categories, $request->parent, null, $request->id);

        // Отдаем ajax
        echo json_encode($albums_categories_list, JSON_UNESCAPED_UNICODE);
        // dd($albums_categories_list);
    }

    // Сортировка
    public function albums_categories_sort(Request $request)
    {

        $i = 1;
        
        foreach ($request->albums_categories as $item) {
            AlbumsCategory::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $albums_category = AlbumsCategory::findOrFail($request->id);
        $albums_category->display = $display;
        $albums_category->save();

        if ($albums_category) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении отображения на сайте!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
