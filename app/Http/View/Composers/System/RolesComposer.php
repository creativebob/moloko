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
        ->filials($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->get();

		return $view->with(compact('roles'));

	}

}
