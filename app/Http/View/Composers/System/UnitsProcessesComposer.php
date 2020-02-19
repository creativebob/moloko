<?php

namespace App\Http\View\Composers\System;

use App\Unit;

use Illuminate\View\View;

class UnitsProcessesComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $units = Unit::where('category_id', $view->units_category_id)
        ->get();

        return $view->with(compact('units'));
    }
}
