<?php

namespace App\Http\Controllers;

use App\Company;
use App\Page;
use App\User;
use App\Department;
use App\RightRole;
use App\Action;
use App\Right;
use App\Entity;
use App\RoleUser;
use App\ActionEntity;

use App\Http\Requests\System\RoleRequest;
use App\Http\Controllers\Session;

// Модели которые отвечают за работу с правами + политики
use App\Role;
use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\UpdateUser;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'roles';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Role::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $roles = Role::with('rights', 'company', 'author')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны в список
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // Получаем авторизованного пользователя
        $user = Auth::user();

        // Определяем количество разрешений
        $counts_directive_array = [];

        foreach ($roles as $role) {
            foreach ($user->roles as $user_role) {

                if($role->id == $user_role->id)
                {
                    $counts_directive_array[$role->id]['disabled_role'] = 'disabled';
                } else {
                    $counts_directive_array[$role->id]['disabled_role'] = '';
                };
            };

            $count_allow = $role->rights->where('directive', 'allow')->count();
            $count_deny = $role->rights->where('directive', 'deny')->count();

            $counts_directive_array[$role->id]['count_allow'] = $count_allow;
            $counts_directive_array[$role->id]['count_deny'] = $count_deny;
        };

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        // dd($counts_directive_array);
        return view('roles.index', compact('roles', 'counts_directive_array', 'pageInfo'));
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Role::class);

        $role = new Role;

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('roles.create', compact('role', 'pageInfo'));
    }


    public function store(RoleRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Role::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();

        $role = new Role;
        $role->name = $request->name;
        $role->description = $request->description;

        if($user->company_id != null){$role->company_id = $user->company_id;} else {$role->company_id = null;};
        $role->author_id = $user->id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $role->moderation = true;
        };

        $role->save();

        if($role){} else {abort(403);};
        return redirect('/admin/roles');
    }


    public function show($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $role = Role::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize('view', $role);

        return view('roles.edit', compact('role'));
    }


    public function edit($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $role = Role::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $role);

        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('roles.edit', compact('role', 'pageInfo'));
    }


    public function update(RoleRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $role = Role::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize('update', $role);

        $role->name = $request->name;
        $role->description = $request->description;

        $role->save();

        return redirect('/admin/roles');
    }

    public function destroy($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $role = Role::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $role);

        $role = Role::destroy($id);
        if ($role) {return Redirect('/admin/roles');} else {echo 'Произошла ошибка';};

    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->roles as $item) {
            Role::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Role::where('id', $request->id)->update(['system' => $system]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $item = Role::where('id', $request->id)->update(['display' => $display]);

        if ($item) {

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

    public function setting(Request $request, $role_id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $role = Role::with('rights')->moderatorLimit($answer)->find($role_id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $role);

        // Получаем авторизованного пользователя
        $user = $request->user();

        $count_role = RoleUser::where('role_id', $role_id)->where('user_id', $user->id)->count();
        if($count_role != 0){abort(403);};

        // Получим все права и их ID в массив
        $allrights = Right::with('actionentity')->get();
        // dd($allrights);

        // Находим все возможные в системе права и кладем их в массив с указанием их ID
        $allrights_array = [];

        foreach ($allrights as $right) {
            $allrights_array[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;
            $f = $right->actionentity->alias_action_entity;
        };

        // РАБОТАЕМ С РАЗРЕШЕНИЯМИ:

        // Создаем массив который будет содержать данные на отображение всех чекбоксов
        // с учетом прав пользователя, и с учетом прав редактируемой роли

        // Инициируем пустой массив для хранения данных построчно.
        // Данные будут выводитья путем разового перебора этого массива
        $main_mass = [];

        // Инициируем пустой массив для хранения данных по чекбоксам в строке
        $boxes = [];

        // Получаем сущности
        $entities = Entity::where('rights', true)->get();
        $actions = Action::get();
        $all_deny_right = Right::with('actionentity')->where('directive', 'deny')->get()->toarray();

        // Получаем права на редактируемую роль
        $current_role = Role::with(['rights' => function($q){$q->where('category_right_id', 1);}])->find($role_id);

        // Создаем ассоциированный массив
        // В формате: Ключ"user-create-allow" и right_id
        $role_access = [];

        foreach ($current_role->rights as $right){
            $role_access[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;
        }

        $session  = session('access');

        if($user->god == 0){

            // Если простой смертный, то получает свои права
            // Смотрим права авторизованного пользователя для филиала в котором он устроен - получаем все права
            $user_filial_id  = session('access')['user_info']['filial_id'];
            $session = $session['all_rights'];

        } else {

            // Если бог, то получает все права
            $session = $session['all_rights'];
        }

        // Наполняем массив данными:
        foreach($entities as $entity){

            // Перебираем все операции действий в системе
            foreach($actions as $action){

                // РАБОТАЕМ С РАЗРЕШЕНИЯМИ: -----------------------------------------------------------------------------------------------
                // Получаем имя искомого разрешения и/или запрета у юзера:
                $box_allow_name = $action->method . '-' . $entity->alias . '-allow';
                $box_deny_name = $action->method . '-' . $entity->alias . '-deny';

                // dd($box_deny_name);

                // Если запись существует, пишем 1, если нет, то 0
                if(isset($session[$box_allow_name])&&(isset($session[$box_deny_name]) == false)){

                    $status_box = '1';
                    $right_id = $session[$box_allow_name]['right_id'];

                    // Если в редактиремой роли присутствует право (которое также присутствует и у авторизованного пользователя),
                    // то ставим галочку
                    if(isset($role_access[$box_allow_name])){

                        $checked = 'checked';
                    } else {

                        $checked = '';
                    }

                    $disabled = '';

                } else {

                    $checked = '';
                    $status_box = '0';
                    $disabled = 'disabled';
                    $right_id = '';
                }

                // dd($user->user_status);
                if(($role->system == null)&&($role->company_id == null)&&($user->god == null)){$disabled = 'disabled';};

                // Формируем строку с данными для чекбоксов на одну сущность
                $boxes[] = [
                    'action_method' => $box_allow_name,
                    'action_id' => $action->id,
                    'action_name' => $action->name,
                    'entity_id' => $entity->id,
                    'status_box' => $status_box,
                    'right_id' => $right_id,
                    'checked' => $checked,
                    'disabled' => $disabled
                ];

                // РАБОТАЕМ С ЗАПРЕТАМИ: -------------------------------------------------------------------------------------------------------

                //Получаем ID текущего права (Чекбокс над которым прохдит операция)
                $right_id = $allrights_array[$box_deny_name];

                // Если запись существует, пишем 1, если нет, то 0
                if(isset($session[$box_deny_name])){
                    $status_box = '1';
                    $disabled = 'disabled';

                } else {

                    // Если в редактиремой роли присутствует право (которое также присутствует и у авторизованного пользователя),
                    // то ставим галочку

                    if(isset($role_access[$box_deny_name])){
                        $status_box = '1';
                        $checked = 'checked';
                    } else {
                        $status_box = '0';
                        $checked = '';
                    }
                }

                if(($role->system == null)&&($role->company_id == null)){$disabled = 'disabled';};

                // Формируем строку с данными для чекбоксов на одну сущность
                $boxes_deny[] = [
                    'action_method' => $box_deny_name,
                    'action_id' => $action->id,
                    'action_name' => $action->name,
                    'entity_id' => $entity->id,
                    'status_box' => $status_box,
                    'right_id' => $right_id,
                    'checked' => $checked,
                    'disabled' => $disabled
                ];

            }

            // Формируем строку разрешений
            $main_mass[] = ['entity_name' => $entity->name, 'entity_id' => $entity->id, 'boxes' => $boxes];

            // Формируем строку запретов
            $main_mass_deny[] = ['entity_name' => $entity->name, 'entity_id' => $entity->id, 'boxes' => $boxes_deny];

            // Чистим массив - готовим для очередной итерации
            $boxes = [];
            $boxes_deny = [];

        }  // Завершение foreach (Наполняем массив данными)

        // dd($main_mass);


        return view('roles.setting', compact('main_mass', 'main_mass_deny', 'actions', 'role_id', 'role'));
    }


    public function setright(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'update');

        // Получаем авторизованного пользователя
        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $role = Role::moderatorLimit($answer)->find($request->role_id);

        // Подключение политики
        $this->authorize('update', $role);

        $role_id = $request->role_id;

        if(
            (($role->system == null)&&($role->company_id != null)&&($user->god == null))|| // ОБЫЧНАЯ ДЛЯ ПОЛЬЗОВАТЕЛЯ
            (($role->system == null)&&($user->god == 1))
        ){

            // СОЗДАНИЕ СВЯЗИ - НАЗНАЧЕНИЕ ПРАВА НА РОЛЬ:
            // Для начала проверим, не работаем ли мы с системной или шаблонной ролью

            if(is_array($request->rights)) {

                // -----------------------------------------------------------------------------------------------------------------------------
                // РАБОТАЕМ С ГРУППОВЫМ НАЗНАЧЕНИЕМ ПРАВА --------------------------------------------------------------------------------------
                // -----------------------------------------------------------------------------------------------------------------------------

                // если пришел массив правил, удаляем все что найдем
                $delete_rights = RightRole::where(['role_id' => $role_id, 'system' => null])->whereIn('right_id', $request->rights)->delete();

                // echo $request->checkbox;
                if ($request->checkbox == false) {

                    // Если чекбокс был снят то все
                    $data = [
                        'status' => 0,
                        'msg' => "Удалили права на сущность!",
                    ];

                } else {
                    // Если чекбокс былвключен, то пишем пришедшие значения
                    // echo $delete_rights;
                    $mass = [];
                    // Смотрим список пришедших роллей
                    foreach ($request->rights as $right) {
                        $mass[] = [
                        'right_id' => $right,
                        'role_id' => $role_id,
                        'author_id' => $user->id,
                        ];
                    };
                    DB::table('right_role')->insert($mass);
                    $data = [
                        'status' => 1,
                        'msg' => "Записали права на сущность!",
                    ];
                };

                // Отдаем результат
                echo json_encode($data, JSON_UNESCAPED_UNICODE);

            } else {

                // -----------------------------------------------------------------------------------------------------------------------------
                // РАБОТАЕМ С ОДИНОЧНЫМ НАЗНАЧЕНИЕМ ПРАВА --------------------------------------------------------------------------------------
                // -----------------------------------------------------------------------------------------------------------------------------

                // Получаем ID связи роли с правом
                $rightrole = RightRole::where('role_id', $request->role_id)->where('right_id', $request->right_id)->first();



                // Если такая связь НАЙДЕНА
                if(isset($rightrole)){


                    // Если запись права в роли не являеться системной, то удаляем ее.
                    if($rightrole->system == 1){

                        $rightrole = RightRole::destroy($rightrole->id);
                        echo "Хуй ты тут что нибудь изменишь! Запись системная!";

                    };

                    // Если запись права в роли не являеться системной, то удаляем ее.
                    if($rightrole->system == null){

                        $rightrole = RightRole::destroy($rightrole->id);
                        echo "Есть такая запись! Наебнули к хуям!";

                    };

                // Если такая связь НЕ найдена
                } else {

                    echo "Такой записи не было. Сделали попытку записать!";

                    $rightrole = new RightRole;
                    $rightrole->role_id = $request->role_id;
                    $rightrole->right_id = $request->right_id;
                    $rightrole->author_id = $user->id;

                    $rightrole->save();

                    if($rightrole){

                    } else {

                        echo "Все пошло по пизде!";
                    };

                }

            }

        }

    }


    // Получаем роль
    public function get_role(Request $request)
    {

        // $role_id = 1;
        // $department_id = 1;
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $role = Role::moderatorLimit($answer)->find($request->role_id);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_departments = operator_right('departments', true, 'index');

        // Главный запрос
        $department = Department::moderatorLimit($answer_departments)->find($request->department_id);

        $role_user = (object) [
            'role' => $role,
            'department' => $department,
            'position' => null,
        ];

        // dd($role_user->position);
        // Отдаем Ajax
        return view('users.roles', ['role_user' => $role_user]);
    }
}
