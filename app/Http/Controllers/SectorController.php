<?php

namespace App\Http\Controllers;

// Модели
use App\Sector;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\SectorRequest;

// Политика
use App\Policies\SectorPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Транслитерация
use Transliterate;

// На удаление
use Illuminate\Support\Facades\Auth;

class SectorController extends Controller
{

    // Настройки сконтроллера
    public function __construct()
    {
        $this->middleware('auth');
        $this->entity_name = 'sectors';
        $this->entity_dependence = false;
        $this->class = Sector::class;
        $this->model = 'App\Sector';
        $this->type = 'modal';
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------
        $items = Sector::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны альбомов
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();
        // ->groupBy('parent_id');
        // dd($sectors);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        $entity = $this->entity_name;
        $class = $this->model;
        $type = $this->type;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Отдаем Ajax
        if ($request->ajax()) {
            $id = $request->id;
            return view('includes.menu_views.category_list', ['items' => $sectors, 'class' => App\Sector::class, 'entity' => $this->entity_name, 'type' => 'modal', 'id' => $id]);
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index', compact('items', 'page_info', 'entity', 'class', 'type', 'filter'));
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);
        $sector = new Sector;
        return view('sectors.create', ['sector' => $sector, 'sector_id' => $request->sector_id]);
    }

    public function store(SectorRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Sector::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем в базу
        $sector = new Sector;
        $sector->company_id = $company_id;
        $sector->author_id = $user_id;

        // Модерация и системная запись
        $sector->system_item = $request->system_item;

        $sector->display = $request->display;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $sector->moderation = 1;
        }

        // Смотрим что пришло
        // Если категория
        if ($request->first_item == 1) {
            $sector->category_status = 1;
        }

        // Если вложенный
        if ($request->medium_item == 1) {
            $sector->parent_id = $request->parent_id;
        }

        // Делаем заглавной первую буквуa
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

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, true, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $sector = Sector::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        return view('sectors.edit', ['sector' => $sector, 'sector_id' => $sector->parent_id]);
    }

    public function update(SectorRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $sector = Sector::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Модерация и системная запись
        $sector->system_item = $request->system_item;
        $sector->moderation = $request->moderation;

        $sector->display = $request->display;

        $sector->parent_id = $request->parent_id;
        $sector->editor_id = $user_id;

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

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $sector = Sector::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $sector);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $sector_parent = Sector::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Если содержит, то даем сообщение об ошибке
        if ($sector_parent) {
            $result = [
                'error_status' => 1,
                'error_message' => 'Данная область содержит населенные пункты, удаление невозможно'
            ];
        } else {

            // Если нет, мягко удаляем
            if ($sector->category_status == 1) {
                $parent = null;
            } else {
                $parent = $sector->parent_id;
            }

            $sector->editor_id = $user_id;
            $sector->save();

            $sector = Sector::destroy($id);

            if ($sector) {
                // Переадресовываем на index
                return redirect()->action('SectorController@index', ['id' => $parent]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при записи сектора!'
                ];
            }
        }
    }

    // Проверка наличия в базе
    public function ajax_check(Request $request)
    {

        // Проверка отдела в нашей базе данных
        $sector = Sector::where('name', $request->name)->first();

        // Если такое название есть
        if ($sector) {
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

    // Список секторов
    public function sectors_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $sectors = Sector::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($sectors);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $sectors_list = get_select_tree($products_categories, $request->parent, null, $request->id);
        // dd($sectors_list);

        // Отдаем ajax
        echo json_encode($sectors_list, JSON_UNESCAPED_UNICODE);

    }
}
