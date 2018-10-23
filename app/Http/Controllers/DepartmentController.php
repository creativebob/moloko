<?php

namespace App\Http\Controllers;

// Модели
use App\Department;
use App\City;
use App\Position;
use App\Staffer;
use App\Page;
use App\Right;
use App\Schedule;
use App\Worktime;
use App\Location;
use App\ScheduleEntity;

// Подрубаем трейт перезаписи сессии
use App\Http\Controllers\Traits\RewriteSessionDepartments;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\DepartmentRequest;

// Политика
use App\Policies\DepartmentPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы
use Illuminate\Support\Facades\Storage;

// На удаление
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    // Подключаем трейт перезаписи списк отделов (филиалов) в сессии пользователя
    use RewriteSessionDepartments;

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'departments';
    protected $entity_dependence = true;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Department::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_departments = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $answer_staff = operator_right('staff', true, getmethod(__FUNCTION__));

        $answer_positions = operator_right('positions', false, 'index');

        // -----------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------

        $departments = Department::with(['staff' => function ($query) use ($answer_staff) {
            $query->moderatorLimit($answer_staff)
            ->companiesLimit($answer_staff)
            ->filials($answer_staff) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
            ->authors($answer_staff)
            ->systemItem($answer_staff) // Фильтр по системным записям
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }, 'staff.position' => function ($query) use ($answer_positions) {
            $query->moderatorLimit($answer_positions)
            ->companiesLimit($answer_positions)
            ->authors($answer_positions)
            ->systemItem($answer_positions) // Фильтр по системным записям
            ->template($answer_positions) // Выводим шаблоны альбомов
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }, 'staff.user'])
        ->moderatorLimit($answer_departments)
        ->companiesLimit($answer_departments)
        ->filials($answer_departments) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer_departments)
        ->systemItem($answer_departments) // Фильтр по системным записям
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get()
        ->groupBy('parent_id');


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------
        
        // Создаем масив где ключ массива является ID меню
        // $departments_rights = [];
        // $departments_rights = $departments->keyBy('id');

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Проверяем прапва на редактирование и удаление
        // $departments_id = [];

        // foreach ($departments_rights as $department) {
        //     $edit = 0;
        //     $delete = 0;

        //     if ($user->can('update', $department)) {
        //         $edit = 1;
        //     }

        //     if ($user->can('delete', $department)) {
        //         $delete = 1;
        //     }

        //     $department_right = $department->toArray();
        //     $departments_id[$department_right['id']] = $department_right;
        //     $departments_id[$department_right['id']]['edit'] = $edit;
        //     $departments_id[$department_right['id']]['delete'] = $delete;

        //     // Проверяем права на удаление
        //     foreach ($department->staff as $id => $staffer) {
        //         $del_staff = 0;

        //         if ($user->can('delete', $staffer)) {
        //             $del_staff = 1;
        //         }

        //         $departments_id[$department_right['id']]['staff'][$id]['delete'] = $del_staff;
        //     }
        // }
        // // dd($departments_id);

        // // Функция построения дерева из массива от Tommy Lacroix
        // $departments_tree = [];

        // foreach ($departments_id as $id => &$node) {   

        //     // Если нет вложений
        //     if (!$node['parent_id']){

        //         $departments_tree[$id] = &$node;
        //     } else { 

        //         // Если есть потомки то перебераем массив
        //         $departments_id[$node['parent_id']]  ['children'][$id] = &$node;
        //     }
        // }

        // foreach ($departments_tree as $department) {
        //     $count = 0;

        //     if (isset($department['children'])) {
        //         $count = count($department['children']) + $count;
        //     }
        //     if (isset($department['staff'])) {
        //         $count = count($department['staff']) + $count;
        //     }
        //     $departments_tree[$department['id']]['count'] = $count;
        //     // dd($department);
        // }
        // dd($departments_tree);

        $entity = $this->entity_name;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // После записи переходим на созданный пункт меню
        if ($request->ajax()) {
            return view('departments.enter', ['grouped_items' => $departments, 'class' => 'App\Department', 'entity' => $this->entity_name, 'type' => 'edit', 'id' => $request->id, 'item' => $request->item]);
        }

        return view('departments.index', compact('departments', 'page_info', 'pages', 'departments', 'filter', 'entity'));
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Department::class);

        $department = new Department;


        if (isset($request->parent_id)) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_departments = operator_right($this->entity_name, $this->entity_dependence, 'index');

            $department = Department::moderatorLimit($answer_departments)->where('id', $request->parent_id)->first();

            if ($department->filial_status == 1) {

                // Если филиал
                $departments = Department::moderatorLimit($answer_departments)
                ->companiesLimit($answer_departments)
                ->filials($answer_departments) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
                ->authors($answer_departments)
                ->systemItem($answer_departments) // Фильтр по системным записям
                ->where('id', $request->parent_id)
                ->orWhere('filial_id', $request->parent_id)
                ->orderBy('sort', 'asc')
                ->get(['id', 'name', 'filial_status', 'parent_id'])
                ->keyBy('id')
                ->toArray();

                $filial_id = $department->id;
            } else {

                // Если отдел
                $departments = Department::moderatorLimit($answer_departments)
                ->companiesLimit($answer_departments)
                ->filials($answer_departments) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
                ->authors($answer_departments)
                ->systemItem($answer_departments) // Фильтр по системным записям
                ->where('id', $department->filial_id)
                ->orWhere('filial_id', $department->filial_id)
                ->orderBy('sort', 'asc')
                ->get(['id', 'name', 'filial_status', 'parent_id'])
                ->keyBy('id')
                ->toArray();

                $filial_id = $department->filial_id;
            }

            // dd($departments);

            // Не выносим в хелпер формирование списка, так так в филиалах не category_status, а filial_status

            // Формируем дерево вложенности
            $departments_cat = get_parents_tree($departments);

            // echo json_encode($departments_cat);
            // dd($departments_cat);

            // Функция отрисовки option'ов
            function tplMenu($item, $padding, $parent) {

                $selected = '';
                if ($item['id'] == $parent) {
                    $selected = ' selected';
                }
                if ($item['filial_status'] == 1) {
                    $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$item['name'].'</option>';
                } else {
                    $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$padding.' '.$item['name'].'</option>';
                }

                // Добавляем пробелы вложенному элементу
                if (isset($item['children'])) {
                    $i = 1;
                    for($j = 0; $j < $i; $j++){
                        $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                    }     
                    $i++;

                    $menu .= showCat($item['children'], $padding, $parent);
                }
                return $menu;
            }

            // Рекурсивно считываем наш шаблон
            function showCat($data, $padding, $parent){
                $string = '';
                $padding = $padding;
                foreach($data as $item){
                    $string .= tplMenu($item, $padding, $parent);
                }
                return $string;
            }

            // Получаем HTML разметку
            $departments_list = showCat($departments_cat, '', $request->parent_id);

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
            // $departments_list = get_select_tree($departments, $request->parent_id, null, null);
            // echo $departments_list;

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_staff = operator_right('staff', 'true', 'index');

            // Смотрим на наличие должности в данном филиале, в массиве устанавливаем id должностей, которых не может быть более 1ой
            $direction = Staffer::where(['position_id' => 1, 'filial_id' => $filial_id])->moderatorLimit($answer_staff)->count();

            $repeat = [];

            if ($direction == 1) {
                $repeat[] = 1;
            }

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_positions = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

            // -------------------------------------------------------------------------------------------
            // ГЛАВНЫЙ ЗАПРОС
            // -------------------------------------------------------------------------------------------
            $positions_list = Position::with('staff')->moderatorLimit($answer_positions)
            ->companiesLimit($answer_positions)
            ->filials($answer_positions) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
            ->authors($answer_positions)
            ->systemItem($answer_positions) // Фильтр по системным записям
            ->template($answer_positions) // Выводим шаблоны в список
            ->whereNotIn('id', $repeat)
            ->pluck('name', 'id');

            $staffer = new Staffer;

            // echo $positions_list;
            // echo $department . ' ' . $positions_list . ' ' . $departments_list;

            return view('departments.create-medium', compact('departments_list', 'positions_list', 'department', 'staffer'));
        } else {


            // Формируем пуcтой массив
            $worktime = [];
            for ($n = 1; $n < 8; $n++){$worktime[$n]['begin'] = null;$worktime[$n]['end'] = null;}

                return view('departments.create-first', compact('department', 'worktime'));
        }
    }

    public function store(DepartmentRequest $request)
    {

        // dd($request);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Department::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $department = new Department;

        if (isset($request->city_name)) {

            // Добавляем локацию
            $location = create_location($request);
            $department->location_id = $location->id;
        }

        $department->company_id = $user->company_id;

        // Имя филиала / отдела
        $first = mb_substr($request->name,0,1, 'UTF-8'); //первая буква
        $last = mb_substr($request->name,1); //все кроме первой буквы
        $first = mb_strtoupper($first, 'UTF-8');
        $department->name = $first.$last;

        $department->author_id = $user_id;

        // Пишем филиал
        if ($request->first_item == 1) {
            $department->filial_status = 1;
            $status = 'филиала';
        }

        // Пишем отделы
        if ($request->medium_item == 1) {
            $department->filial_id = $request->filial_id;
            $department->parent_id = $request->parent_id;
            $status = 'отдела';
        }

        // Отображение на сайте
        $department->display = $request->display;

        $department->save();

        // Если пришел хотя бы один день из расписания пишем расписание и дни
        if (isset($request->mon_begin)||isset($request->tue_begin)||isset($request->wed_begin)||isset($request->thu_begin)||isset($request->fri_begin)||isset($request->sat_begin)||isset($request->sun_begin)) {
            $schedule = new Schedule;
            $schedule->company_id = $user->company_id;
            $schedule->name = 'График работы для '.$status.': '.$department->name;
            $schedule->description = null;
            $schedule->author_id = $user_id;
            $schedule->save();
            $schedule_id = $schedule->id;

            // Функция getWorktimes ловит все поля расписания из запроса и готовит к записи в worktimes
            $mass_time = getWorktimes($request, $schedule_id);

            // Записываем в базу все расписание.
            DB::table('worktimes')->insert($mass_time);

            // Создаем связь расписания с филиалом / отделом
            $schedule_entity = new ScheduleEntity;
            $schedule_entity->schedule_id = $schedule->id;
            $schedule_entity->entity_id = $department->id;
            $schedule_entity->entity = 'departments';
            $schedule_entity->save();
        }

        if ($department) {

            // Телефон
            $phones = add_phones($request, $department);

            // Перезаписываем сессию: меняем список филиалов и отделов на новый
            $this->RSDepartments($user);

            // Переадресовываем на index
            return redirect()->action('DepartmentController@index', ['id' => $department->id, 'item' => 'departments']);

            // $action_method = "DepartmentController@get_content";
            // $action_arrray = ['id' => $department->id, 'item' => 'department'];
            // return redirect()->action('GetAccessController@set', ['action_method' => $action_method, 'action_arrray' => $action_arrray]);

        } else {
            abort(403, 'Ошибка при записи отдела!');
        }
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $department = Department::with('location', 'schedules.worktimes', 'main_phone')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $department);

        if(isset($department->schedules->first()->worktimes)){
            $worktime_mass = $department->schedules->first()->worktimes->keyBy('weekday');
        }

        for($x = 1; $x<8; $x++){

            if(isset($worktime_mass[$x]->worktime_begin)){

                $worktime_begin = $worktime_mass[$x]->worktime_begin;
                $str_worktime_begin = secToTime($worktime_begin);
                $worktime[$x]['begin'] = $str_worktime_begin;

            } else {

                $worktime[$x]['begin'] = null;
            }

            if(isset($worktime_mass[$x]->worktime_interval)){

                $worktime_interval = $worktime_mass[$x]->worktime_interval;

                if(($worktime_begin + $worktime_interval) > 86400){

                    $str_worktime_interval = secToTime($worktime_begin + $worktime_interval - 86400);
                } else {

                    $str_worktime_interval = secToTime($worktime_begin + $worktime_interval);                       
                }

                $worktime[$x]['end'] = $str_worktime_interval;
            } else {

                $worktime[$x]['end'] = null;
            }

        }

        if ($department->filial_status == 1) {

            // Меняем филиал
            return view('departments.edit-first', compact('department', 'worktime'));
        } else {

            // Меняем отдел
            $item_id = $department->id;
            $filial_id = $department->filial_id;
            $parent_id = $department->parent_id;

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $departments = Department::moderatorLimit($answer)
            ->where('id', $filial_id)
            ->orWhere('filial_id', $filial_id)
            ->orderBy('sort', 'asc')
            ->get(['id', 'name', 'filial_status', 'parent_id'])
            ->keyBy('id')
            ->toArray();

            // echo $departments;

            // Не выносим в хелпер формирование списка, так так в филиалах не category_status, а filial_status

            // Формируем дерево вложенности
            $departments_cat = get_parents_tree($departments);

            // echo json_encode($departments_cat, JSON_UNESCAPED_UNICODE);

            // Функция отрисовки option'ов
            function tplMenu($item, $padding, $id, $parent) {

                // echo json_encode($item, JSON_UNESCAPED_UNICODE);
                // Убираем из списка пришедший отдел 
                if ($item['id'] != $id) {

                    $selected = '';
                    if ($item['id'] == $parent) {
                        $selected = ' selected';
                    }
                    if ($item['filial_status'] == 1) {
                        $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$item['name'].'</option>';
                    } else {
                        $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$padding.' '.$item['name'].'</option>';
                    }

                    // Добавляем пробелы вложенному элементу
                    if (isset($item['children'])) {
                        $i = 1;
                        for($j = 0; $j < $i; $j++){
                            $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                        }     
                        $i++;

                        $menu .= showCat($item['children'], $padding, $id, $parent);
                    }
                    return $menu;
                }
            }

            // Рекурсивно считываем наш шаблон
            function showCat($data, $padding, $id, $parent){
                $string = '';
                $padding = $padding;

                foreach($data as $item){
                    $string .= tplMenu($item, $padding, $id, $parent);
                }
                return $string;
            }

            // echo $item_id . ' ' . json_encode($departments_cat, JSON_UNESCAPED_UNICODE);

            // echo $parent_id;
            // Получаем HTML разметку
            $departments_list = showCat($departments_cat, '', $item_id, $parent_id);

            // echo json_encode($departments_list);
            // echo $department . ' ' . $departments_list;

            return view('departments.edit-medium', compact('department', 'departments_list', 'worktime'));
        } 
    }

    public function update(DepartmentRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__))
        ;

        // ГЛАВНЫЙ ЗАПРОС:
        $department = Department::with('location', 'schedules.worktimes')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $department);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        if (isset($department->location_id)) {

            // Обновляем локацию
            $location = update_location($request, $department);
            // Если пришла другая локация, то переписываем
            if ($department->location_id != $location->id) {
                $department->location_id = $location->id;
            }
        }

        // Имя филиала / отдела
        $first = mb_substr($request->name,0,1, 'UTF-8'); //первая буква
        $last = mb_substr($request->name,1); //все кроме первой буквы
        $first = mb_strtoupper($first, 'UTF-8');
        $department->name = $first.$last;

        // Телефон
        $phones = add_phones($request, $department);

        $department->editor_id = $user_id;

        $status = 'филиала';
        if ($request->medium_item == 1) {
            $department->parent_id = $request->parent_id;
            $status = 'отдела';
        }

        // Отображение на сайте
        $department->display = $request->display;

        $department->save();

        // Если не существует расписания для компании - создаем его
        if($department->schedules->count() < 1){

            $schedule = new Schedule;
            $schedule->company_id = $user->company_id;
            $schedule->name = 'График работы для '.$status.': ' . $department->name;
            $schedule->description = null;
            $schedule->save();

            // Создаем связь расписания с компанией
            $schedule_entity = new ScheduleEntity;
            $schedule_entity->schedule_id = $schedule->id;
            $schedule_entity->entity_id = $department->id;
            $schedule_entity->entity = 'departments';
            $schedule_entity->save();

            $schedule_id = $schedule->id;
        } else {

            $schedule_id = $department->schedules->first()->id;
        }

        // Функция getWorktimes ловит все поля расписания из запроса и готовит к записи в worktimes
        $mass_time = getWorktimes($request, $schedule_id);

        // Удаляем все записи времени в worktimes для этого расписания
        $worktimes = Worktime::where('schedule_id', $schedule_id)->forceDelete();

        // Вставляем новое время в расписание
        DB::table('worktimes')->insert($mass_time);

        if ($department) {
            // Переадресовываем на index
            return redirect()->action('DepartmentController@index', ['id' => $department->id, 'item' => 'department']);
        } else {
            abort(403, 'Ошибка при обновлении отдела!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $department = Department::withCount('staff')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $department);

        // Удаляем ajax
        // Проверяем содержит ли филиал / отдел вложения / должности
        $department_parent = Department::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        if (isset($department_parent) || ($department->staff_count > 0)) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Данный отдел не пуст, удаление невозможно'
            ];
        } else {

            // Если нет, мягко удаляем
            if ($department->filial_status == 1) {
                $parent = null;
            } else {
                $parent = $department->parent_id;
            }

            $department->editor_id = $user->id;
            $department->save();

            $department = Department::destroy($id); 

            if ($department) {

                // Перезаписываем сессию: меняем список филиалов и отделов на новый
                $this->RSDepartments($user);

                // Переадресовываем на index
                return redirect()->action('DepartmentController@index', ['id' => $parent, 'item' => 'department']);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при удалении!'
                ];
            }
        }
    }

    public function departments_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, true, 'index');

        $departments_list = Department::moderatorLimit($answer)->whereId($request->filial_id)
        ->orWhere('filial_id', $request->filial_id)
        ->pluck('name', 'id');

        echo json_encode($departments_list, JSON_UNESCAPED_UNICODE);
    }

    public function ajax_check(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Проверка отдела в нашей базе данных
        $department = Department::where(['name' => $request->name, 'company_id' => $user->company_id])->first();

        $res = false;
        if ($department) {

            // Если такой филиал существует в компании
            if (isset($department->filial_status)) {
                $res = true;
            }
            if ($department->filial_id == $request->filial_id) {
                $res = true;
            }
        }

        if ($res) {
            $result = [
                'error_message' => 'Такой отдел уже существует',
                'error_status' => 1,
            ];
        } else {
            $result = [
                'error_status' => 0,
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}