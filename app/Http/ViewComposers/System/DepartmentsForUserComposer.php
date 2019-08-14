<?php

namespace App\Http\ViewComposers\System;

use Illuminate\View\View;

class DepartmentsForUserComposer
{
	public function compose(View $view)
	{

		$departments_list = getLS('users', 'view', 'departments');
		return $view->with('departments_list', $departments_list);

	}

}