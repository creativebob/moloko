<?php

namespace App\Http\ViewComposers;

use App\Period;

use Illuminate\View\View;

class PeriodsComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $periods = Period::orderBy('sort', 'asc')
        ->get();

        return $view->with('periods', $periods);
    }

}