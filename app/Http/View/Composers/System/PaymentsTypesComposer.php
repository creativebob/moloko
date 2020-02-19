<?php

namespace App\Http\View\Composers\System;

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
