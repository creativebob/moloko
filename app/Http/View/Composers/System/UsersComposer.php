<?php

namespace App\Http\View\Composers\System;

use App\User;

use Illuminate\View\View;

class UsersComposer
{
	public function compose(View $view)
	{

        // Список пользователей
        $answer = operator_right('users', true, 'index');

        $users = User::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->whereNull('god')
        ->orderBy('second_name')
        ->get();

        return $view->with('users', $users);
    }

}
