<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show()
    {

    	if (Auth::check()) {
    		return view('user');
    	}
    	return view('layouts.enter');
    }

    public function create(Request $request)
    {

  //   	$login = $request->login;
  //   	$email = $request->email;
  //   	$password = $request->password;
  //   	$nickname = $request->nickname;

  //   	$first_name =   $request->first_name;
  //   	$second_name = $request->second_name;
  //   	$patronymic = $request->patronymic;
		// $sex = $request->sex;
		// $birthday = $request->birthday;

  //   	$phone = $request->phone;
  //   	$extra_phone = $request->extra_phone;
  //   	$telegram_id = $request->telegram_id;
  //   	$city_id = $request->city_id;
  //   	$address = $request->address;

  //   	$orgform_status = $request->orgform_status;
  //   	$company_name = $request->company_name;
  //   	$inn = $request->inn;
  //   	$kpp = $request->kpp;
  //       $account_settlement = $request->account_settlement;
  //     	$account_correspondent = $request->account_correspondent;
  //       $bank = $request->bank;

  //   	$passport_address = $request->passport_address;
  //   	$passport_number = $request->passport_number;
  //   	$passport_released = $request->passport_released;
  //   	$passport_date = $request->passport_date;

  //   	$contragent_status = $request->contragent_status;
  //   	$lead_id = $request->lead_id;
  //   	$employee_id = $request->employee_id;
  //   	$block_access = $request->block_access;

    	$user = new User;

    	$user->login = $request->login;
    	$user->email = $request->email;
    	$user->password = $request->password;
    	$user->nickname = $request->nickname;

    	$user->first_name =   $request->first_name;
    	$user->second_name = $request->second_name;
    	$user->patronymic = $request->patronymic;
		$user->sex = $request->sex;
		$user->birthday = $request->birthday;

    	$user->phone = cleanPhone($request->phone);
    	$user->extra_phone = cleanPhone($request->extra_phone);
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
    	$user->block_access = $request->block_access;

		$user->save();
		return view('users');
    }

}
