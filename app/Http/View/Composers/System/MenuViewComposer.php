<?php

namespace App\Http\View\Composers\System;

use Illuminate\View\View;

class MenuViewComposer
{
	public function compose(View $view)
	{
		return $view->with('categories', buildTree($view->items));
	}
}
