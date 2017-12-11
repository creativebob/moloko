<?php

namespace App\Http\Controllers;


use App\User;
use App\Company;
use App\Access;
use App\Access_group;
use App\Http\Requests\UpdateUser;

use App\Policies\UserPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function destroy($id)
    {
		$user = User::destroy($id);
	    if ($user){
	    $data = [
	      'status'=> 1,
	      'msg' => 'Успешно удалено'
	    ];
	    } else {
	      $data = [
	      'status' => 0,
	      'msg' => 'Произошла ошибка'
	    ];
	    };
	    echo json_encode($data, JSON_UNESCAPED_UNICODE);

      	Log::info('Удалили запись' . $id);
    }


    public function index(Request $request)
    {
		if (Auth::user()->can('index', Auth::user())) {

            // Проверка на статус пользователя: должен быть сотрудником - 1
	    	if($request->contragent_status){
	    		$users = User::Сontragent($request->contragent_status)->AccessBlock($request->access_block)->paginate(30);
	    		return view('users.index', compact('users'));
	    	} else {
	    		$users = User::paginate(30);
	    		return view('users.index', compact('users'), compact('access'));
	    	}


	 		} else {
		    	abort(403, 'Просмотр запрещено!');
		    };
    }

    public function store(UpdateUser $request)
    {
        if(Auth::user()->can('create', Auth::user())){
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

    	$user->contragent_status = $request->contragent_status;
    	$user->lead_id = $request->lead_id;
    	$user->employee_id = $request->employee_id;
    	$user->access_block = $request->access_block;

    	$user->group_action_id = $request->group_action_id;
    	$user->group_locality_id = $request->group_locality_id;
        $user->save();

        $user_id = $user->id;

        // Создаем компанию под пользователя
        // Если стоит отметка о том, что нужно создать компанию.
        if($user->orgform_status == '1'){

            //Проверим по ИНН есть ли компания в базе
            $company_inn = Company::where('company_inn', $user->user_inn)->count();
            if($company_inn == 1){
                // Компания существует
                
            } else {
                // Компания не существует

            $company = new Company;
            $company->company_name = $request->company_name;
            $company->kpp = $request->kpp;
            $company->account_settlement = $request->account_settlement;
            $company->account_correspondent = $request->account_correspondent;
            $company->bank = $request->bank;
            $company->user_id = $user_id;

            $company->save();
            };

        } else{
        // Когда отметки нет
         
        };





		return redirect('users');

        } else {
            abort(403, 'Запись невозможна - недостаточно прав!');
        }


    }

    //
    public function create()
    {
    	$users = new User;
        $access_action_list = Access_group::where('category_right_id', '1')->pluck('access_group_name', 'id');
        $access_locality_list = Access_group::where('category_right_id', '2')->pluck('access_group_name', 'id');
        $access_groups = new Access_group;

    	return view('users.create', compact('users', 'access_groups', 'access_action_list', 'access_locality_list'));
    }

    public function update(UpdateUser $request, $id)
    {

    	$user = User::findOrFail($id);
    	$access = new Access;

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
    	// $user->company_name = $request->company_name;
    	$user->user_inn = $request->inn;
    	// $user->kpp = $request->kpp;
     // 	$user->account_settlement = $request->account_settlement;
     // 	$user->account_correspondent = $request->account_correspondent;
     //  	$user->bank = $request->bank;

    	$user->passport_address = $request->passport_address;
    	$user->passport_number = $request->passport_number;
    	$user->passport_released = $request->passport_released;
    	$user->passport_date = $request->passport_date;

    	$user->contragent_status = $request->contragent_status;
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
        $access_action_list = Access_group::where('category_right_id', '1')->pluck('access_group_name', 'id');
        $access_locality_list = Access_group::where('category_right_id', '2')->pluck('access_group_name', 'id');
        $access_groups = new Access_group;

    	$users = User::findOrFail($id);
    	return view('users.show', compact('users', 'access_groups', 'access_action_list', 'access_locality_list'));
    }

    public function edit($id)
    {

        $access_action_list = Access_group::where('category_right_id', '1')->pluck('access_group_name', 'id');
        $access_locality_list = Access_group::where('category_right_id', '2')->pluck('access_group_name', 'id');
        $access_groups = new Access_group;

        $users = User::findOrFail($id);
         Log::info('Позырили страницу Users, в частности смотрели пользователя с ID: '.$id);
         return view('users.edit', compact('users', 'access_groups', 'access_action_list', 'access_locality_list'));
    }

}
