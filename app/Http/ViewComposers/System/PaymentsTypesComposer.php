<?php

namespace App\Http\ViewComposers\System;

use App\PaymentsType;

use Illuminate\View\View;
class PaymentsTypesComposer
{
	public function compose(View $view)
	{
        $payments_types = PaymentsType::get();

        return $view->with(compact('payments_types'));
    }

}
