<?php

namespace App\Http\ViewComposers;

use App\CatalogsType;

use Illuminate\View\View;

class CatalogsTypesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right('catalogs', false, 'index');

        // Главный запрос
        $catalogs_types = CatalogsType::get();

        return $view->with(compact('catalogs_types'));
    }

}