<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class MenuViewComposer
{
	public function compose(View $view)
	{
		return $view->with('categories', buildTree($view->items));
	}
}