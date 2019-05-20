<?php

namespace App\Http\ViewComposers;

use App\Unit;

use Illuminate\View\View;

class UnitsTmcComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $units = Unit::where('units_category_id', 2)
        ->get();

        return $view->with(compact('units'));
    }
}
