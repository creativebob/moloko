<?php

namespace App\Http\ViewComposers;

use App\Unit;

use Illuminate\View\View;

class UnitsComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $units = Unit::with('units_category')
        ->where('units_category_id', $view->units_category_id)
        ->get();

        $units_attributes = $units->mapWithKeys(function ($item) {
            return [$item->id => ['data-abbreviation' => $item->abbreviation]];
        })->all();

        return $view->with(compact('units', 'units_attributes'));
    }
}
