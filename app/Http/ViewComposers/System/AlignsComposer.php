<?php

namespace App\Http\ViewComposers\System;

use App\Align;

use Illuminate\View\View;
class AlignsComposer
{
	public function compose(View $view)
	{
        $aligns = Align::get();

        return $view->with(compact('aligns'));
    }

}
