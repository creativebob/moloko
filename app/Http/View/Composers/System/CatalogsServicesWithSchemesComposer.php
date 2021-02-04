<?php

namespace App\Http\View\Composers\System;

use App\CatalogsService;
use Illuminate\View\View;

class CatalogsServicesWithSchemesComposer
{
	public function compose(View $view)
	{
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_services', false, 'index');

        // Главный запрос
        $catalogsServices = CatalogsService::with([
            'agency_schemes'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();
        // dd($catalogs_goods);

        return $view->with(compact('catalogsServices'));
    }

}
