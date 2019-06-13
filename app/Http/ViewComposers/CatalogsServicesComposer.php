<?php

namespace App\Http\ViewComposers;

use App\CatalogsService;
use App\Department;

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
        ->get();
        // dd($catalogs_services);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('departments', true, 'index');

        // Главный запрос
        $filials = Department::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->whereNull('parent_id')
        ->get();

        return $view->with(compact('catalogs_services', 'filials'));
    }

}
