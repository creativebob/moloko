<?php

namespace App\Http\View\Composers\System;

use App\CompaniesSettingsCategory;

use Illuminate\View\View;

class CompaniesSettingsCategoriesWithSettingsComposer
{
	public function compose(View $view)
	{
        $settingsCategories = CompaniesSettingsCategory::with([
            'settings'
        ])
        ->get();

        return $view->with(compact('settingsCategories'));
    }

}
