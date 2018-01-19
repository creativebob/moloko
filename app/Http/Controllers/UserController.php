<?php

namespace App\Http\Controllers;

use App\User;
use App\Company;
use App\Page;
use App\Right;
use App\RoleUser;
use App\Department;
use App\Http\Controllers\Session;
use App\Scopes\ModerationScope;

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

        // Подключение политики
        // $this->authorize('index', User::class);
        
        $user = Auth::user();

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $operator_answer = operator_right('users', true);
        // dd($operator_answer);

        // Получаем читаемый список филиалов для SELECT в формах
        $list_filials = Department::whereIn('id', $operator_answer['filials'])->pluck('department_name', 'id');

        // Получаем ID авторизованного пользователя из сессии если не хотим использовать Auth::user();
        // $user = $operator_answer['user_id'];






        // Тут типа кодим ...






        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        if((isset($operator_answer['company_id']))&&($user->god == null))
        {
            // Если у пользователя есть компания
            $users = User::withoutGlobalScope($operator_answer['moderator'])
            ->whereCompany_id($operator_answer['company_id'])
            ->filials($operator_answer['filials'], $operator_answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
            ->whereGod(null)
            ->authors($operator_answer['authors'])
            ->systemItem($operator_answer['system_item']) // Фильтр по системным записям
            ->orderBy('moderated', 'desc')
            ->paginate(30);

        } elseif($user->god == 1) {

            // Вы вероятно под выбранной компании
            // 
            $filials = null;
            $system_item = 1;
            $authors = null;

            $users = User::withoutGlobalScope(ModerationScope::class)
            ->orderBy('moderated', 'desc')
            ->paginate(30);

        } else {

            abort(403, 'Пошел нахуй!');

        };

	    return view('users.index', compact('users', 'access', 'session'));
	}




    //
    public function create()
    {

        // Подключение политики
        // $this->authorize('index', User::class);
        
        $user = Auth::user();

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $operator_answer = operator_right('users', true);
        // dd($operator_answer);

        // Получаем читаемый список филиалов для SELECT в формах
        $list_filials = Department::whereIn('id', $operator_answer['filials'])->pluck('department_name', 'id');

        // Получаем ID авторизованного пользователя из сессии если не хотим использовать Auth::user();
        // $user = $operator_answer['user_id'];

    	$user = new User;
        $roles = new Role;
    	return view('users.create', compact('user', 'roles', 'list_filials'));
    }


    public function store(UpdateUser $request)
    {

        // Подключение политики
        // $this->authorize('index', User::class);
        
        $auth_user = Auth::user();

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $operator_answer = operator_right('users', true);
        // dd($operator_answer);

        // Получаем читаемый список филиалов для SELECT в формах
        $list_filials = Department::whereIn('id', $operator_answer['filials'])->pluck('department_name', 'id');

        // Получаем ID авторизованного пользователя из сессии если не хотим использовать Auth::user();
        // $user = $operator_answer['user_id'];
        // 

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

        if($operator_answer['automoderate'] == false){$user->moderation = 1;};

        // Если у пользователя есть назначенная компания и пользователь не являеться богом
        if(isset($auth_user->company_id)&&($auth_user->god != 1)){
            $user->company_id = $auth_user->company_id;
            $user->filial_id = $session['user_info']['filial_id'];

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




    public function update(UpdateUser $request, $id)
    {

        // Подключение политики
        // $this->authorize('update', $user);
        
        $user_auth = Auth::user();

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $operator_answer = operator_right('users', true);
        // dd($operator_answer);


        $user = User::withoutGlobalScope($operator_answer['moderator'])->findOrFail($id);


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

        if($operator_answer['automoderate']){$user->moderated = null;} else {$user->moderated = 1;};

		$user->save();
 
		return redirect('users');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        // $this->authorize('view', $user);

        $roles = new Role;
    	return view('users.show', compact('user', 'roles'));
    }

    public function edit($id)
    {

        // Подключение политики
        // $this->authorize('update', $user);
        
        $user_auth = Auth::user();

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $operator_answer = operator_right('users', true);
        // dd($operator_answer);

        // Получаем читаемый список филиалов для SELECT в формах
        $list_filials = Department::whereIn('id', $operator_answer['filials'])->pluck('department_name', 'id');

        // Получаем читаемый список отделов для SELECT в формах
        $departments = Department::whereIn('id', $operator_answer['departments'])->pluck('department_name', 'id');

        // Получаем ID авторизованного пользователя из сессии если не хотим использовать Auth::user();
        // $user = $operator_answer['user_id'];

        // $operator_answer['moderator'] = ModerationScope::class;
        // dd($operator_answer['moderator']);

        $user = User::withoutGlobalScope($operator_answer['moderator'])->findOrFail($id);
        // dd($user);

        $role = new Role;
        $role_users = RoleUser::whereUser_id($id)->get();

        $roles = Role::whereCompany_id($user_auth->company_id)->pluck('role_name', 'id');
        // $departments = Department::whereCompany_id($user_auth->company_id)->pluck('department_name', 'id');
        
        Log::info('Позырили страницу Users, в частности смотрели пользователя с ID: '.$id);
        return view('users.edit', compact('user', 'role', 'role_users', 'roles', 'departments', 'list_filials'));
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


    public function getauthcompany($company_id)
    {

        // $this->authorize('update', $user);

        $auth_user = Auth::user();

        if($auth_user->god == 1){
            $auth_user->company_id = $company_id;
            $auth_user->save();         
        }
        return redirect('companies');
    }


    public function getauthuser(Request $request, $user_id)
    {

        // $this->authorize('update', $user);

        $auth_user = Auth::user();

        if($auth_user->god == 1){
            session(['god' => $auth_user->id]);
            Auth::loginUsingId($user_id);
        };

        return redirect('/getaccess');
    }

    public function getgod()
    {
        // $this->authorize('update', User::class); 
        if(Auth::user()->god == 1){
            $user = User::findOrFail(Auth::user()->id);
            $user->company_id = null;
            $user->save();
        }
        return redirect('companies');
    }

    public function returngod(Request $request)
    {
        
        if ($request->session()->has('god')) {

            $god_id = $request->session()->get('god');
            $request->session()->forget('god');
            Auth::loginUsingId($god_id);


        }

        return redirect('/getaccess');
    }

}
