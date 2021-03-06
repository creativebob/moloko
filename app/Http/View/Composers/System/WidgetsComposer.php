<?php

namespace App\Http\View\Composers\System;

use App\Widget;
use Illuminate\View\View;

class WidgetsComposer
{
	public function compose(View $view)
	{
        $widgets = Widget::get();
        return $view->with(compact('widgets'));
    }

}
