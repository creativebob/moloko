<?php

namespace App\Http\View\Composers\System;

use App\Supplier;
use Illuminate\View\View;

class SuppliersComposer
{
	public function compose(View $view)
	{
		dd('Тут');
        $suppliers = Supplier::get();
        return $view->with(compact('suppliers'));
    }
}
