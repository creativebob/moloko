<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class ViewMenuComposer
{
	public function compose(View $view)
	{

		$categories = buildTree($view->items);
		return $view->with(compact('categories'));
	}
}