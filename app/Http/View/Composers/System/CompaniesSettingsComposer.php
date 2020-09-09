<?php

namespace App\Http\View\Composers\System;

use App\CompaniesSetting;

use Illuminate\View\View;

class CompaniesSettingsComposer
{
	public function compose(View $view)
	{
        $settings = CompaniesSetting::get();


        return $view->with(compact('settings'));
    }

}
