<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    //
    public function show()
    {
    	$users = User::get();
    	return view('users', compact('users'));
    }
}
