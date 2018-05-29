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

// Специфические классы 

// На удаление
use Illuminate\Support\Facades\Auth;

class SectorController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'sectors';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Sector::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        $sectors = Sector::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны альбомов
        ->booklistFilter($request)
        ->orderBy('sort', 'asc')
        ->get();

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $filter_query = Sector::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get();

        $filter['status'] = null;

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);
        // dd($filter);

        // ---------------------------------------------------------------------------------------------------------------------------------------------

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем массив с вложенными элементами дял отображения дерева с правами, отдаем обьекты сущности и авторизованного пользователя
        $sectors_tree = get_index_tree_with_rights($sectors, $user);
        // dd($sectors_tree);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        
        // Отдаем Ajax
        if ($request->ajax()) {
            return view('sectors.category-list', ['sectors_tree' => $sectors_tree, 'id' => $request->id]);
        }

        return view('sectors.index', compact('sectors_tree', 'page_info', 'filter'));
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Sector::class);

        $sector = new Sector;

        if (isset($request->parent_id)) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $sectors = Sector::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->orderBy('sort', 'asc')
            ->get(['id','name','category_status','parent_id'])
            ->keyBy('id')
            ->toArray();

            // dd($sectors);

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $sectors_list = get_select_tree($sectors, $request->parent_id, null, null);

            return view('sectors.create-medium', ['sector' => $sector, 'sectors_list' => $sectors_list]);
        } else {
            return view('sectors.create-first', ['sector' => $sector]);
        }
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

        if ($sector->category_status == 1) {

            // Меняем категорию
            return view('sectors.edit-first', ['sector' => $sector]);
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $sectors = Sector::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->orderBy('sort', 'asc')
            ->get(['id','name','category_status','parent_id'])
            ->keyBy('id')
            ->toArray();

            // dd($sectors);

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $sectors_list = get_select_tree($sectors, $sector->parent_id, null, null);

            return view('sectors.edit-medium', ['sector' => $sector, 'sectors_list' => $sectors_list]);
        }
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

        $sector->parent_id = $request->parent_id;
        $sector->editor_id = $user_id;

        // Делаем заглавной первую букву
        $sector->name = get_first_letter($request->name);

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
    public function sector_check(Request $request)
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

  // Сортировка
    public function sectors_sort(Request $request)
    {
        $result = '';
        $i = 1;
        foreach ($request->sectors as $item) {

            $sector = Sector::findOrFail($item);
            $sector->sort = $i;
            $sector->save();

            // if ($sector) {
            //   $result = [
            //     'error_status' => 0,
            //   ];
            // } else {
            //   $result = [
            //     'error_status' => 1,
            //     'msg' => 'Произошла непредвиденная ошибка, попробуйте перезагрузить страницу и попробуйте еще раз'
            //   ];
            // }
            // return json_encode($result, JSON_UNESCAPED_UNICODE);

            $i++;
        }
    }
}
