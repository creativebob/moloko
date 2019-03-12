<?php

namespace App\Http\ViewComposers;

use App\Catalog;

use Illuminate\View\View;

class CatalogsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs', false, 'index');

        // Главный запрос
        $catalogs = Catalog::with('items')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();

        return $view->with('catalogs', $catalogs);
    }

}