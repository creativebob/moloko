<?php

namespace App\Http\Controllers;

use App\User;
use App\Company;
use App\Page;
use App\Right;
use App\RoleUser;
use App\Department;


// Модели которые отвечают за работу с правами + политики
use App\Role;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUser;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function index(Request $request)
    {
        // $this->authorize('index', User::class);
        $user = Auth::user();
        $others_item['user_id'] = $user->id;
        $system_item = null;

        // Смотрим права на простотр системных.
        //  foreach ($user->roles as $role) {
        //     foreach ($role->rights as $right) {
        //         // Перебор всех прав пользователя
        //         if ($right->category_right_id == 3) {$others_item[$right->right_action] = $right->right_action;};
        //         if ($right->right_action == 'system-user') {$system_item = 1;};
        //         if ($right->right_action == 'get-users') {$others_item['all'] = 'all';};
        //         if ($right->right_action == 'get-depertments') {$others_item['all'] = 'all';}; 
        //     }
        // }

        if (isset($user->company_id)) {
            // Если у пользователя есть компания
            $users = User::whereCompany_id($user->company_id)
                    // ->whereGod(null)
                    // ->otherItem($others_item)
                    // ->systemItem($system_item) // Фильтр по системным записям
                    ->paginate(30);
        } else {
            // Если нет, то бог без компании
            if ($user->god == 1) {
              $users = User::paginate(30);
            };
        }
        // dd($users);

        $menu = Page::get();
	    return view('users.index', compact('users', 'access', 'menu'));
	}

    public function store(UpdateUser $request)
    {
        // $this->authorize('create', User::class);

        $auth_user = Auth::user();
    	$user = new User;

    	$user->login = $request->login;
    	$user->email = $request->email;
    	$user->password = bcrypt($request->password);
    	$user->nickname = $request->nickname;

    	$user->first_name =   $request->first_name;
    	$user->second_name = $request->second_name;
    	$user->patronymic = $request->patronymic;
		$user->sex = $request->sex;
	   	$user->birthday = $request->birthday;

    	$user->phone = cleanPhone($request->phone);

    	if(($request->extra_phone != Null)&&($request->extra_phone != "")){
    		$user->extra_phone = cleanPhone($request->extra_phone);
    	};

    	$user->telegram_id = $request->telegram_id;
    	$user->city_id = $request->city_id;
    	$user->address = $request->address;

    	$user->orgform_status = $request->orgform_status;

    	$user->user_inn = $request->inn;

    	$user->passport_address = $request->passport_address;
    	$user->passport_number = $request->passport_number;
    	$user->passport_released = $request->passport_released;
    	$user->passport_date = $request->passport_date;

    	$user->user_type = $request->user_type;
    	$user->lead_id = $request->lead_id;
    	$user->employee_id = $request->employee_id;
    	$user->access_block = $request->access_block;

        $user->author_id = $auth_user->id;

        // Если у пользователя есть назначенная компания и пользователь не являеться богом
        if(isset($auth_user->company_id)&&($auth_user->god != 1)){
            $user->company_id = $auth_user->company_id;
        // Если бог авторизован под компанией
        } elseif(isset($auth_user->company_id)&&($auth_user->god == 1)) {
            $user->company_id = $auth_user->company_id;
        } elseif(($auth_user->company_id == null) && ($auth_user->god == 1)){
            $user->system_item = 1;
        } else {
            abort(403);
        };

        $user->save();


        // // Создаем компанию под пользователя
        // // Если стоит отметка о том, что нужно создать компанию.
        // if($user->orgform_status == '1'){

        //     //Проверим по ИНН есть ли компания в базе
        //     $company_inn = Company::where('company_inn', $user->user_inn)->count();
        //     if($company_inn == 1){
        //         // Компания существует
                
        //     } else {
        //         // Компания не существует

        //     $company = new Company;
        //     $company->company_name = $request->company_name;
        //     $company->kpp = $request->kpp;
        //     $company->account_settlement = $request->account_settlement;
        //     $company->account_correspondent = $request->account_correspondent;
        //     $company->bank = $request->bank;
        //     $company->user_id = $user_id;

        //     $company->save();
        //     };

        // } else{

        // // Когда отметки нет
         
        // };

		return redirect('users');
    }

    //
    public function create()
    {
        // $this->authorize('create', User::class);

    	$user = new User;
        $roles = new Role;
        $menu = Page::get();
    	return view('users.create', compact('user', 'roles', 'menu'));
    }

    public function update(UpdateUser $request, $id)
    {

        $user = User::findOrFail($id);
        // $this->authorize('update', $user);

    	$user->login = $request->login;
    	$user->email = $request->email;
    	$user->password = bcrypt($request->password);
    	$user->nickname = $request->nickname;

    	$user->first_name =   $request->first_name;
    	$user->second_name = $request->second_name;
    	$user->patronymic = $request->patronymic;
		$user->sex = $request->sex;
	 	$user->birthday = $request->birthday;

    	$user->phone = cleanPhone($request->phone);

    	if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
    		$user->extra_phone = cleanPhone($request->extra_phone);
    	} else {$user->extra_phone = NULL;};

    	$user->telegram_id = $request->telegram_id;
    	$user->city_id = $request->city_id;
    	$user->address = $request->address;

    	$user->orgform_status = $request->orgform_status;

    	$user->user_inn = $request->inn;

    // $user->company_name = $request->company_name;
    // $user->kpp = $request->kpp;
    // $user->account_settlement = $request->account_settlement;
    // $user->account_correspondent = $request->account_correspondent;
    // $user->bank = $request->bank;

    	$user->passport_address = $request->passport_address;
    	$user->passport_number = $request->passport_number;
    	$user->passport_released = $request->passport_released;
    	$user->passport_date = $request->passport_date;

    	$user->user_type = $request->user_type;
    	$user->lead_id = $request->lead_id;
    	$user->employee_id = $request->employee_id;
    	$user->access_block = $request->access_block;

    	$user->group_action_id = $request->group_action_id;
    	$user->group_locality_id = $request->group_locality_id;

		$user->save();
 
		return redirect('users');
    	// $users = User::all();

    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        // $this->authorize('view', $user);

        $roles = new Role;
        $menu = Page::get();
    	return view('users.show', compact('user', 'roles', 'menu'));
    }

    public function edit($id)
    {
        $auth_user = Auth::user();
        $user = User::findOrFail($id);
        // $this->authorize('update', $user);

        // $access_action_list = Role::where('category_right_id', '1')->pluck('role_name', 'id');
        // $access_locality_list = Role::where('category_right_id', '2')->pluck('role_name', 'id');
        $role = new Role;
        $menu = Page::get();
        $role_users = RoleUser::whereUser_id($id)->get();

        $roles = Role::whereCompany_id($auth_user->company_id)->pluck('role_name', 'id');
        $departments = Department::whereCompany_id($auth_user->company_id)->pluck('department_name', 'id');

        // dd($roles);
        
        Log::info('Позырили страницу Users, в частности смотрели пользователя с ID: '.$id);
        return view('users.edit', compact('user', 'role', 'menu', 'role_users', 'roles', 'departments'));
    }


    public function destroy($id)
    {
        // Удаляем пользователя с обновлением
        $user = User::destroy($id);
        if ($user) {
          return Redirect('/users');
        } else {
          echo 'Произошла ошибка';
        }; 
    }


    public function getauth($id, $company_id)
    {
        $user = User::findOrFail($id);
        // $this->authorize('update', $user);

        $user->company_id = $company_id;
        $user->save();
 
        return redirect('/companies');
    }

    public function getgod()
    {
        // $this->authorize('update', User::class); 
        if(Auth::user()->god == 1){
            $user = User::findOrFail(Auth::user()->id);
            $user->company_id = null;
            $user->save();
        }
        return redirect('/companies');
    }
}
