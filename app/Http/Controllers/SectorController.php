<?php

namespace App\Http\Controllers;

// Модели
use App\Sector;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\SectorRequest;

// Транслитерация
use Transliterate;

// На удаление
use Illuminate\Support\Facades\Auth;

class SectorController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Sector $sector)
    {
        $this->middleware('auth');
        $this->sector = $sector;
        $this->class = Sector::class;
        $this->model = 'App\Sector';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'modal';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Отдаем Ajax
        if ($request->ajax()) {

            $id = $request->id;
            return view('includes.menu_views.category_list',
                [
                    'items' => $this->sector->getIndex($answer, $request),
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => count($this->sector->getIndex($answer, $request)),
                    'id' => $request->id
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $this->sector->getIndex($answer, $request),
                'page_info' => $page_info,
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'filter' => $filter
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
            'title' => 'Добавление сектора',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(SectorRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Пишем в базу
        $sector = new Sector;
        $sector->company_id = $user->company_id;
        $sector->author_id = hideGod($user);

        // Модерация и системная запись
        $sector->system_item = $request->system_item;
        $sector->display = $request->display;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $sector->moderation = 1;
        }

        $sector->parent_id = $request->parent_id;
        $sector->category_id = $request->category_id;

        // Делаем заглавной первую букву
        $sector->name = get_first_letter($request->name);

        $sector->tag = empty($request->tag) ? Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]) : $request->tag;

        $sector->save();

        if ($sector) {
            // Переадресовываем на index
            return redirect()->action('SectorController@index', ['id' => $sector->id]);
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

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $sector = $this->sector->getItem(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)), $id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        return view('includes.menu_views.edit', [
            'item' => $sector,
            'entity' => $this->entity_alias,
            'title' => 'Редактирование сектора',
            'parent_id' => $sector->parent_id,
            'category_id' => $sector->category_id
        ]);
    }

    public function update(SectorRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $sector = $this->sector->getItem(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)), $id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        // Модерация и системная запись
        $sector->system_item = $request->system_item;
        $sector->display = $request->display;
        $sector->moderation = $request->moderation;

        $sector->parent_id = $request->parent_id;
        $sector->editor_id = hideGod($request->user());

        // Делаем заглавной первую букву
        $sector->name = get_first_letter($request->name);

        $sector->tag = empty($request->tag) ? Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]) : $request->tag;

        $sector->save();

        if ($sector) {
            // Переадресовываем на index
            return redirect()->action('SectorController@index', ['id' => $sector->id]);
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
        $sector = $this->sector->getItem(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)), $id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        // Проверяем содержит ли сектор вложения
        $sectors_count = Sector::moderatorLimit(operator_right($this->entity_alias, true, getmethod(__FUNCTION__)))
        ->whereParent_id($sector->id)
        ->count();

        // Если содержит, то даем сообщение об ошибке
        if ($sectors_count > 0) {
            $result = [
                'error_status' => 1,
                'error_message' => 'Категория содержит вложенные элементы, удаление невозможно!'
            ];
        } else {

            $parent = $sector->parent_id;

            // Скрываем бога
            $sector->editor_id = hideGod($request->user());
            $sector->save();

            $sector = Sector::destroy($id);

            if ($sector) {
                // Переадресовываем на index
                return redirect()->action('SectorController@index', ['id' => $parent]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при удалении сектора!'
                ];
            }
        }
    }

    // Список секторов
    // public function sectors_list(Request $request)
    // {

    //     // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    //     $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

    //     // Главный запрос
    //     $sectors = Sector::moderatorLimit($answer)
    //     ->companiesLimit($answer)
    //     ->authors($answer)
    //     ->systemItem($answer) // Фильтр по системным записям
    //     ->get(['id','name','category_status','parent_id'])
    //     ->keyBy('id')
    //     ->toArray();

    //     // dd($sectors);

    //     // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
    //     $sectors_list = get_select_tree($products_categories, $request->parent, null, $request->id);
    //     // dd($sectors_list);

    //     // Отдаем ajax
    //     echo json_encode($sectors_list, JSON_UNESCAPED_UNICODE);
    // }
}
