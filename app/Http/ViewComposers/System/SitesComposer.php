<?php

namespace App\Http\ViewComposers\System;

use App\Site;

use Illuminate\View\View;

class SitesComposer
{
	public function compose(View $view)
	{

        // Список меню для сайта
        $answer = operator_right('sites', false, 'index');

        $sites = Site::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
//        ->orWhereNull('company_id')
        // ->systemItem($answer) // Фильтр по системным записям
        ->get([
            'id',
            'name'
        ]);

        return $view->with(compact('sites'));

    }
}
