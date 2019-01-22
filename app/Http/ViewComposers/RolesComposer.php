<?php

namespace App\Http\ViewComposers;

use App\Role;
use Illuminate\View\View;

class RolesComposer
{
	public function compose(View $view)
	{

		$answer_roles = operator_right('roles', false, 'index');

        $roles_list = Role::moderatorLimit($answer_roles)
        ->companiesLimit($answer_roles)
        ->filials($answer_roles) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer_roles)
        ->systemItem($answer_roles) // Фильтр по системным записям
        ->template($answer_roles) // Выводим шаблоны в список
        ->pluck('name', 'id');

		return $view->with('roles_list', $roles_list);

	}

}