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

// Модели которые отвечают за работу с правами + политики
use App\Role;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUser;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize('index', User::class);
        $user = Auth::user();
        $others_item['user_id'] = $user->id;
        $system_item = null;

        // // Смотрим права на простотр системных.
        //  foreach ($user->roles as $role) {
        //     foreach ($role->rights as $right) {
        //         // Перебор всех прав пользователя
        //         if ($right->category_right_id == 3) {$others_item[$right->right_action] = $right->right_action;};
        //         if ($right->right_action == 'system-users') {$system_item = 1;};
        //         if ($right->right_action == 'getall-users') {$others_item['all'] = 'all';};
        //     }
        // }

        if (isset($user->company_id)) {
            // Если у пользователя есть компания
            $roles = Role::whereCompany_id($user->company_id)
                    // ->otherItem($others_item)
                    ->systemItem($system_item) // Фильтр по системным записям
                    ->paginate(30);

        } else {
            // Если нет, то бог без компании
            if ($user->god == 1) {
              $roles = Role::paginate(30);
            };
        }


        // Определяем количество разрешений 
        $counts_directive_array = [];
        foreach ($roles as $role) {

            foreach ($user->roles as $user_role) {

                if($role->id == $user_role->id){
                    $disabled_role = 'disabled';
                } else {
                    $disabled_role = '';
                };
            }


            $count_allow = $role->rights->where('directive', 'allow')->count();
            $count_deny = $role->rights->where('directive', 'deny')->count();

            $counts_directive_array[$role->id]['count_allow'] = $count_allow;
            $counts_directive_array[$role->id]['count_deny'] = $count_deny;
            $counts_directive_array[$role->id]['disabled_role'] = $disabled_role;
        };

        return view('roles.index', compact('roles', 'counts_directive_array'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->authorize('create', Role::class);

        $user = Auth::user();
        $departments_list = Department::where('company_id', $user->company_id)->whereFilial_status(1)->pluck('department_name', 'id');

        $role = new Role;

        return view('roles.create', compact('role', 'departments_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Role::class);

        $user = Auth::user();
        $role = new Role;
        $role->role_name = $request->role_name;
        $role->role_description = $request->role_description;
        if(isset($user->company_id)){ $role->company_id = $user->company_id;} else { $role->system_item = 1;};
        $role->author_id = $user->id;
        $role->save();
        if($role){

            // Блок на удаление
            // $right_role = new RightRole;
            // $right_role->role_id = $role->id;
            // $right_id = Right::whereRight_action($request->department_id)->first();
            // $right_role->right_id = $right_id->id;
            // $right_role ->save();

        } else {abort(403);}

        return redirect('roles');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        // $this->authorize('update', $role);

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $user = Auth::user();
        $departments_list = Department::where('company_id', $user->company_id)->whereFilial_status(1)->pluck('department_name', 'id');
        // $this->authorize('update', $entity);

        return view('roles.show', compact('role', 'departments_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        // $this->authorize('update', $role);
        $role->role_name = $request->role_name;
        $role->role_description = $request->role_description;

        $role->save();
        return redirect('roles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Удаляем роль с обновлением
        $role = Role::destroy($id);
        if ($role) {
          return Redirect('roles');
        } else {
          echo 'Произошла ошибка';
        }; 
    }

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setting($role_id)
    {

        $user = Auth::user();

        $count_role = RoleUser::where('role_id', $role_id)->where('user_id', $user->id)->count();
        if($count_role != 0) {abort(403);};

        // Получим все права и их ID в массив
        $allrights = Right::get();

        // Находим все возможные в системе права и кладем их в массив с указанием их ID
        $allrights_array = [];
        foreach ($allrights as $right) {
            $allrights_array[$right->actionentity->alias_action_entity . "-" . $right->directive] = $right->id;
            $f = $right->actionentity->alias_action_entity;
        }


        // РАБОТАЕМ С РАЗРЕШЕНИЯМИ:
        
        // Создаем массив который будет содержать данные на отображение всех чекбоксов
        // с учетом прав пользователя, и с учетом прав редактируемой роли

        // Инициируем пустой массив для хранения данных построчно. 
        // Данные будут выводитья путем разового перебора этого массива
        $main_mass = [];

        // Инициируем пустой массив для хранения данных по чекбоксам в строке
        $boxes = [];

        // Получаем сущности
        $entities = Entity::get();
        $actions = Action::get();
        $all_deny_right = Right::where('directive', 'deny')->get()->toarray();

        // dd($right);


        // Получаем права на редактируемую роль
        $current_role = Role::with(['rights' => function($q)
        {
            $q->where('category_right_id', 1);
        }])->findOrFail($role_id);

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
            $session = $session['filial_rights'][$user_filial_id];

        } else {

            // Если бог, то получает все права
            $session = $session['all_rights'];
        };


        // Наполняем массив данными:
        foreach($entities as $entity){

            // Перебираем все операции действий в системе 
            foreach($actions as $action){



                // РАБОТАЕМ С РАЗРЕШЕНИЯМИ: -----------------------------------------------------------------------------------------------
                // Получаем имя искомого разрешения и/или запрета у юзера:
                $box_allow_name = $action->action_method . '-' . $entity->entity_alias . '-allow';
                $box_deny_name = $action->action_method . '-' . $entity->entity_alias . '-deny';

                // Если запись существует, пишем 1, если нет, то 0
                if(isset($session[$box_allow_name])&&(isset($session[$box_deny_name]) == false)){
                    $status_box = '1';
                    $right_id = $session[$box_allow_name]['right_id'];

                    // Если в редактиремой роли присутствует право (которое также присутствует и у авторизованного пользователя),
                    // то ставим галочку
                    
                    if(isset($role_access[$box_allow_name])){
                        $checked = 'checked';
                    } else {
                        $checked='';
                    };
                    
                        $disabled = '';


                } else {
                    $checked = '';
                    $status_box = '0';
                    $disabled = 'disabled';
                    $right_id = '';
                };

                // Формируем строку с данными для чекбоксов на одну сущность
                $boxes[] = [
                    'action_method' => $box_allow_name, 
                    'action_id' => $action->id, 
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
                    };
                };


                // Формируем строку с данными для чекбоксов на одну сущность
                $boxes_deny[] = [
                    'action_method' => $box_deny_name, 
                    'action_id' => $action->id, 
                    'entity_id' => $entity->id, 
                    'status_box' => $status_box, 
                    'right_id' => $right_id, 
                    'checked' => $checked, 
                    'disabled' => $disabled
                ];

            }

        // Формируем строку разрешений
            $main_mass[] = ['entity_name' => $entity->entity_name, 'entity_id' => $entity->id, 'boxes' => $boxes];

        // Формируем строку запретов
            $main_mass_deny[] = ['entity_name' => $entity->entity_name, 'entity_id' => $entity->id, 'boxes' => $boxes_deny];


            // Чистим массив - готовим для очередной итерации
            $boxes = [];
            $boxes_deny = [];

        }  // Завершение foreach (Наполняем массив данными)




        // dd($main_mass_deny);

        // 
        return view('roles.setting', compact('main_mass', 'main_mass_deny', 'actions', 'role_id'));
    }


    public function setright(Request $request)
    {
        $user = Auth::user();
        $role_id = $request->role_id;
        // echo $request->rights;
        if (count($request->rights) > 0) {
            // если пришел массив правил, удаляем все что найдум
            $delete_rights = RightRole::where(['role_id' => $role_id, 'system_item' => null])->whereIn('right_id', $request->rights)->delete();
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
            $rightrole = RightRole::where('role_id', $request->role_id)->where('right_id', $request->right_id)->first();

            if(isset($rightrole)){

                    // Если запись права в роли не являеться системной, то удаляем ее.
                    if($rightrole->system_item == null){
                        $rightrole = RightRole::destroy($rightrole->id);
                        echo "Есть такая запись! Сделали попытку ебнуть ее!";                
                    };

            } else {

                    echo "Такой записи не было. Сделали попытку записать!";

                    $rightrole = new RightRole;
                    $rightrole->role_id = $request->role_id;
                    $rightrole->right_id = $request->right_id;
                    $rightrole->author_id = $user->id;

                    $rightrole->save();

                    if($rightrole){

                    } else { echo "Все пошло по пизде!"; }

            };
        };
        
        

    }

}
