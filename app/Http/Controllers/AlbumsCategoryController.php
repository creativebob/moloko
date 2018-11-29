<?php

namespace App\Http\Controllers;

// Модели
use App\AlbumsCategory;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\AlbumsCategoryRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

class AlbumsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(AlbumsCategory $albums_category)
    {
        $this->middleware('auth');
        $this->albums_category = $albums_category;
        $this->entity_alias = with(new AlbumsCategory)->getTable();;
        $this->entity_dependence = false;
        $this->class = AlbumsCategory::class;
        $this->model = 'App\AlbumsCategory';
        $this->type = 'modal';
    }

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Отдаем Ajax
        if ($request->ajax()) {

            $id = $request->id;
            return view('includes.menu_views.category_list',
                [
                    'items' => $this->albums_category->getIndex($answer, $request),
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => count($this->albums_category->getIndex($answer, $request)),
                    'id' => $request->id,
                    'nested' => 'albums_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $this->albums_category->getIndex($answer, $request),
                'page_info' => $page_info,
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'albums_count',
            ]
        );
    }

    public function create(Request $request)
    {

       // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('includes.menu_views.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории альбомов',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(AlbumsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $albums_category = $this->storeCategory($request);

        $albums_category->save();

        if ($albums_category) {

            // Переадресовываем на index
            return redirect()->action('AlbumsCategoryController@index', ['id' => $albums_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории альбомов!'
            ];
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $albums_category = $this->albums_category->getItem(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)), $id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albums_category);

        return view('includes.menu_views.edit', [
            'item' => $albums_category,
            'entity' => $this->entity_alias,
            'title' => 'Редактирование категории альбомов',
            'parent_id' => $albums_category->parent_id,
            'category_id' => $albums_category->category_id
        ]);
    }

    public function update(AlbumsCategoryRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $albums_category = $this->albums_category->getItem(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)), $id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albums_category);

        // Заполнение и проверка основных полей в трейте
        $albums_category = $this->updateCategory($request, $albums_category);

        $albums_category->save();

        if ($albums_category) {

            // Переадресовываем на index
            return redirect()->action('AlbumsCategoryController@index', ['id' => $albums_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории альбомов!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        $albums_category = $this->albums_category->getItem($answer, $id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $albums_category);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $albums_categories_count = AlbumsCategory::moderatorLimit($answer)
        ->whereParent_id($id)
        ->count();

        // Если содержит, то даем сообщение об ошибке
        if ($albums_categories_count > 0) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Категория содержит вложенные элементы, удаление невозможно!'
            ];
        } else {

            $parent = $albums_category->parent_id;

            $albums_category->editor_id = hideGod($request->user());
            $albums_category->save();

            $albums_category = AlbumsCategory::destroy($id);

            if ($albums_category) {

                // Переадресовываем на index
                return redirect()->action('AlbumsCategoryController@index', ['id' => $parent]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при удалении категории альбомов!'
                ];
            }
        }
    }

    // Список категорий альбомов
    public function albums_category_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

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
}
