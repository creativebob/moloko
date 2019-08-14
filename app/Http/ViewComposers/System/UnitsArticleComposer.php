<?php

namespace App\Http\ViewComposers\System;

use App\Unit;

use Illuminate\View\View;

class UnitsArticleComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $units = Unit::where('category_id', $view->units_category_id)
        ->get();

        return $view->with(compact('units'));
    }
}
