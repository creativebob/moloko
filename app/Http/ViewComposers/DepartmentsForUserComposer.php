<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class DepartmentsForUserComposer
{
	public function compose(View $view)
	{

		$departments_list = getLS('users', 'view', 'departments');
		return $view->with('departments_list', $departments_list);

	}

}