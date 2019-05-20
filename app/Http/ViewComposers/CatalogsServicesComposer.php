<?php

namespace App\Http\ViewComposers;

use App\CatalogsService;

use Illuminate\View\View;

class CatalogsServicesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_services', false, 'index');

        $catalogs_type = $view->type;

        // Главный запрос
        $catalogs_services = CatalogsService::with('items')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();

        return $view->with(compact('catalogs_services'));
    }

}
