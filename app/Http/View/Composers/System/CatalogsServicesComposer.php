<?php

namespace App\Http\View\Composers\System;

use App\CatalogsService;

use Illuminate\View\View;

class CatalogsServicesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_services', false, 'index');

        // Главный запрос
        $catalogs_services = CatalogsService::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->toBase()
        ->get();
        // dd($catalogs_services);

        return $view->with(compact('catalogs_services'));
    }

}
