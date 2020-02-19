<?php

namespace App\Http\View\Composers\System;

use Illuminate\View\View;

class DepartmentsViewComposer
{
	public function compose(View $view)
	{
		return $view->with('departments', buildTree($view->departments));
	}
}
