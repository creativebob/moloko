<?php

namespace App\Http\ViewComposers\System;

use App\Unit;

use Illuminate\View\View;

class UnitsComposer
{
	public function compose(View $view)
	{
        // dd($view->units_category_id);
        // Главный запрос
        $units = Unit::with('category')
        ->where('category_id', $view->units_category_id)
        ->get();

        $units_attributes = $units->mapWithKeys(function ($item) {
            return [$item->id => ['data-abbreviation' => $item->abbreviation]];
        })
        ->all();

        return $view->with(compact('units', 'units_attributes'));
    }
}
