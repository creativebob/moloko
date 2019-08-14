<?php

namespace App\Http\ViewComposers\System;

use Illuminate\View\View;

class AccordionsComposer
{
	public function compose(View $view)
	{
		return $view->with('categories', buildTree($view->items));
	}
}
