<?php

namespace App\Http\ViewComposers\System;

use App\Charge;
use Illuminate\View\View;

class ChargesComposer
{
	public function compose(View $view)
	{
        $charges = Charge::get();
        return $view->with(compact('charges'));
    }

}
