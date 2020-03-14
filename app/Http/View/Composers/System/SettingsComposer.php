<?php

namespace App\Http\View\Composers\System;

use App\Setting;

use Illuminate\View\View;

class SettingsComposer
{
	public function compose(View $view)
	{
        $settings = Setting::get();


        return $view->with(compact('settings'));
    }

}
