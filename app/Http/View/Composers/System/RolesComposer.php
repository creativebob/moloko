<?php

namespace App\Http\View\Composers\System;

use App\Role;
use Illuminate\View\View;

class RolesComposer
{
	public function compose(View $view)
	{

		$answer = operator_right('roles', false, 'index');

        $roles = Role::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны в список
        ->get();

		return $view->with(compact('roles'));

	}

}
