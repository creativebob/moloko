<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class DepartmentsViewComposer
{
	public function compose(View $view)
	{
		return $view->with('departments', buildTree($view->departments));
	}
}