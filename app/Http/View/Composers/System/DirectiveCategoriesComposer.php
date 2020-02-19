<?php

namespace App\Http\View\Composers\System;

use App\UnitsCategory;
use Illuminate\View\View;

class DirectiveCategoriesComposer
{
	public function compose(View $view)
	{
        $directive_categories = UnitsCategory::whereIn('alias', [
            'weight',
            'volume'
        ])
        ->get();

        return $view->with(compact('directive_categories'));
    }

}
