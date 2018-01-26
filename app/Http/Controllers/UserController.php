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
        $this->authorize('index', User::class);
        
        $user = Auth::user();

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $answer = operator_right('users', true);
        // dd($answer);

        // Получаем читаемый список филиалов для SELECT в формах
        if($user->god == null){$list_filials = Department::whereIn('id', $answer['filials'])->pluck('department_name', 'id');};

        // Получаем ID авторизованного пользователя из сессии если не хотим использовать Auth::user();
        // $user = $answer['user_id'];



        // Тут типа кодим ...



        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------


        $users = User::withoutGlobalScope($answer['moderator'])
        ->companies($answer['company_id'])
        ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer['all_authors'])
        ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
        ->orWhere('id', $user->id) // Только для сущности USERS
        ->orderBy('moderated', 'desc')
        ->paginate(30);

	    return view('users.index', compact('users', 'access', 'session'));
	}


    //
    public function create()
    {
        // Подключение политики
        $this->authorize('create', User::class);
        
        $user = Auth::user();

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $answer = operator_right('users', true);
        // dd($answer);

        // ПОДГОТОВКА СПИСКОВ ФИЛИАЛОВ И ОТДЕЛОВ КОМПАНИИ ДЛЯ SELECT ----------------------------------------------------------------------------

        // Функция из Helper отдает массив со списками для SELECT (На нее отправляем id компании, для того чтобы бог получил все ее филиалы)
        $list_departments = getListsDepartments($user->company_id);

        $list_filials = $list_departments['list_filials'];
        $list_departments = $list_departments['list_departments'];

        // ЗАВЕРШЕНИЕ ПОДГОТОВКИ СПИСКОВ -----------------------------------------------------------------------------------------------------------------------------

    	$user = new User;
        $roles = new Role;
    	return view('users.create', compact('user', 'roles', 'list_filials'));
    }


    public function store(UpdateUser $request)
    {

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $answer = operator_right('users', true);

        // Получаем данные для авторизованного пользователя из сессии если не хотим использовать Auth::user();
        // $user_auth_id = $answer['user_id'];
        // $user_status = $answer['user_status'];
        // $company_id = $answer['company_id'];
        // $filial_id = $answer['filial_id'];

        // Получаем данные для авторизованного пользователя дополнительным запросом к базе.
        $user_auth = Auth::user();
        $user_auth_id = $user_auth->id;
        $user_status = $user_auth->god;
        $company_id = $user_auth->company_id;
        $filial_id = $user_auth->filial_id;

        // Подключение политики
        $this->authorize('create', User::class);
        // ПОДГОТОВКА СПИСКОВ ФИЛИАЛОВ И ОТДЕЛОВ КОМПАНИИ ДЛЯ SELECT ----------------------------------------------------------------------------


        // ПОЛУЧЕНИЕ И СОХРАНЕНИЕ ДАННЫХ
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

        $user->author_id = $user_auth->id;

        if($answer['automoderate'] == false){
            $user->moderation = 1;
        };

        // Пишем ID компании авторизованного пользователя
        if($company_id == null){abort(403, 'Необходимо авторизоваться под компанией');};
        $user->company_id = $company_id;

        // Пишем ID филиала авторизованного пользователя
        if($filial_id == null){abort(403, 'Необходимо авторизоваться под компанией');};
        $user->filial_id = $filial_id;


        // // Если у пользователя есть назначенная компания и пользователь не являеться богом
        // if(isset($user_auth->company_id)&&($user_auth->god != 1)){
        //     $user->company_id = $company_id;
        //     $user->filial_id = $filial_id;

        // // Если бог авторизован под компанией
        // } elseif(isset($user_auth->company_id)&&($user_auth->god == 1)) {
        //     $user->company_id = $user_auth->company_id;

        // } elseif(($user_auth->company_id == null) && ($user_auth->god == 1)){
        //     $user->system_item = 1;
        // } else {
        //     abort(403);
        // };

        $user->save();
        return redirect('users');
    }


    public function update(UpdateUser $request, $id)
    {

        // Подключение политики
        $this->authorize('update', User::class);
        
        $user_auth = Auth::user();

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $answer = operator_right('users', true);
        // dd($answer);


        $user = User::withoutGlobalScope($answer['moderator'])->findOrFail($id);


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

        if($answer['automoderate']){$user->moderated = null;} else {$user->moderated = 1;};

		$user->save();
 
		return redirect('users');
    }

    public function show($id)
    {

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $answer = operator_right('users', true);

        // Получаем ID авторизованного пользователя из сессии если не хотим использовать Auth::user();
        $user_auth_id = $answer['user_id'];
        // $user_auth = Auth::user();

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::withoutGlobalScope($answer['moderator'])
        ->companies($answer['company_id'])
        ->filials($answer['filials'], $answer['dependence']) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer['all_authors'])
        ->systemItem($answer['system_item'], $answer['user_status'], $answer['company_id']) // Фильтр по системным записям
        ->find($id);

        // if(!isset($user)){abort(403, "Не достаточно прав!");};

        // Подключение политики
        $this->authorize('view', $user);
        
        $roles = new Role;
    	return view('users.show', compact('user', 'roles'));
    }

    public function edit($id)
    {

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $answer = operator_right('users', true);

        // Получаем ID авторизованного пользователя из сессии если не хотим использовать Auth::user();
        // $user_auth_id = $answer['user_id'];
        // $user_status = $answer['user_status'];

        $user_auth = Auth::user();
        $user_auth_id = $user_auth->id;
        $user_status = $user_auth->god;

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::findOrFail($id);

        // Подключение политики
        $this->authorize('update', $user);


        // ПОДГОТОВКА СПИСКОВ ФИЛИАЛОВ И ОТДЕЛОВ КОМПАНИИ ДЛЯ SELECT ----------------------------------------------------------------------------

        // Функция из Helper отдает массив со списками для SELECT (На нее отправляем id компании, для того чтобы бог получил все ее филиалы)
        $list_departments = getListsDepartments($user_auth->company_id);

        $list_filials = $list_departments['list_filials'];
        $list_departments = $list_departments['list_departments'];

        // ЗАВЕРШЕНИЕ ПОДГОТОВКИ СПИСКОВ -----------------------------------------------------------------------------------------------------------------------------

        
        $role = new Role;
        $role_users = RoleUser::whereUser_id($id)->get();

        $roles = Role::whereCompany_id($answer['company_id'])->pluck('role_name', 'id');
        // $departments = Department::whereCompany_id($user_auth->company_id)->pluck('department_name', 'id');
        
        Log::info('Позырили страницу Users, в частности смотрели пользователя с ID: '.$id);
        return view('users.edit', compact('user', 'role', 'role_users', 'roles', 'list_departments', 'list_filials'));
    }


    public function destroy($id)
    {

        // Делаем запрос к оператору прав и передаем ему имя сущности - функция operator_right() получает данные из сессии, анализирует права и отдает результат анализа
        // в виде массива с итогами. Эти итоги используються ГЛАВНЫМ запросом.
        $answer = operator_right('users', true);

        // Получаем ID авторизованного пользователя из сессии если не хотим использовать Auth::user();
        // $user_auth_id = $answer['user_id'];
        // $user_status = $answer['user_status'];

        $user_auth = Auth::user();
        $user_auth_id = $user_auth->id;
        $user_status = $user_auth->god;

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::findOrFail($id);

        
        // Подключение политики
        $this->authorize('delete', $user);

        // Удаляем пользователя с обновлением
        $user = User::destroy($id);

        if($user) {
          return Redirect('/users');
        } else {
          abort(403,'Что-то пошло не так!');
        };
    }


    public function getauthcompany($company_id)
    {

        // $this->authorize('update', User::class);

        $auth_user = Auth::user();

        if($auth_user->god == 1){
            $auth_user->company_id = $company_id;
            $auth_user->save();         
        }
        return redirect('/getaccess/companies.index');
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
        return redirect('/getaccess');
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
