<?php

namespace App\Http\View\Composers\System;

use App\OutletsSettingsCategory;
use Illuminate\View\View;

class OutletsSettingsCategoriesWithSettingsComposer
{
	public function compose(View $view)
	{
        $settingsCategories = OutletsSettingsCategory::with([
            'settings'
        ])
        ->get();

        return $view->with(compact('settingsCategories'));
    }

}
