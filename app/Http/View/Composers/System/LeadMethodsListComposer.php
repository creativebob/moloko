<?php

namespace App\Http\View\Composers\System;

use App\LeadMethod;

use Illuminate\View\View;

class LeadMethodsListComposer
{
	public function compose(View $view)
	{

        // Главный запрос
        $lead_methods_list = LeadMethod::get()->pluck('name', 'id');

        return $view->with('lead_methods_list', $lead_methods_list);
    }

}
