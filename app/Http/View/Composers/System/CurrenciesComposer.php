<?php

namespace App\Http\View\Composers\System;

use App\Currency;

use Illuminate\View\View;

class CurrenciesComposer
{
	public function compose(View $view)
	{
        $currencies = Currency::get();

        return $view->with(compact('currencies'));
    }

}
