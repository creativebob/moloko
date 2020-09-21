<?php

namespace App\Http\View\Composers\System;

use App\Department;
use App\CatalogsService;

use Illuminate\View\View;

class FilialsForCatalogsServicesComposer
{
	public function compose(View $view)
	{

        $catalog = CatalogsService::find($view->catalog_id);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('departments', true, 'index');

        // Главный запрос
        $filials = Department::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->whereNull('parent_id')
        ->where('company_id', $catalog->company_id)
        ->get();
        // dd($filials);

        return $view->with(compact('filials'));
    }

}
