<?php

namespace App\Http\ViewComposers;

use App\UnitsCategory;

use Illuminate\View\View;

class UnitsCategoriesComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $units_categories = UnitsCategory::orderBy('sort', 'asc')
        ->where($view->type, true)
        ->get();

        return $view->with('units_categories', $units_categories);
    }

}
