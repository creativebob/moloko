<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function index()
    {
    	$users = User::paginate(30);
    	return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
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
    	$user->company_name = $request->company_name;
    	$user->inn = $request->inn;
    	$user->kpp = $request->kpp;
      $user->account_settlement = $request->account_settlement;
     	$user->account_correspondent = $request->account_correspondent;
      $user->bank = $request->bank;

    	$user->passport_address = $request->passport_address;
    	$user->passport_number = $request->passport_number;
    	$user->passport_released = $request->passport_released;
    	$user->passport_date = $request->passport_date;

    	$user->contragent_status = $request->contragent_status;
    	$user->lead_id = $request->lead_id;
    	$user->employee_id = $request->employee_id;
    	$user->access_block = $request->access_block;

    	$user->group_users_id = $request->group_users_id;
    	$user->group_filials_id = $request->group_filials_id;

    	

		$user->save();

		return redirect('users');
    }

    //
    public function create()
    {
      $users = new User;
    	return view('users.create', compact('users'));
    }

    public function update(Request $request, $id)
    {

    	$user = User::findOrFail($id);

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
    	$user->company_name = $request->company_name;
    	$user->inn = $request->inn;
    	$user->kpp = $request->kpp;
      $user->account_settlement = $request->account_settlement;
     	$user->account_correspondent = $request->account_correspondent;
      $user->bank = $request->bank;

    	$user->passport_address = $request->passport_address;
    	$user->passport_number = $request->passport_number;
    	$user->passport_released = $request->passport_released;
    	$user->passport_date = $request->passport_date;

    	$user->contragent_status = $request->contragent_status;
    	$user->lead_id = $request->lead_id;
    	$user->employee_id = $request->employee_id;
    	$user->access_block = $request->access_block;

    	$user->group_users_id = $request->group_users_id;
    	$user->group_filials_id = $request->group_filials_id;

		$user->save();
 
		return redirect('users');
    	// $users = User::all();
    }

    public function show($id)
    {
    	$users = User::findOrFail($id);
    	return view('users.show', compact('users'));
    }

    public function edit($id)
    {
      $users = User::findOrFail($id);
      return view('users.edit', compact('users'));
    }


}
