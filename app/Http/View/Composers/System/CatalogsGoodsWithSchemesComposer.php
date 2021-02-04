<?php

namespace App\Http\View\Composers\System;

use App\CatalogsGoods;
use Illuminate\View\View;

class CatalogsGoodsWithSchemesComposer
{
	public function compose(View $view)
	{
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_goods', false, 'index');

        // Главный запрос
        $catalogsGoods = CatalogsGoods::with([
            'agency_schemes'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();
        // dd($catalogs_goods);

        return $view->with(compact('catalogsGoods'));
    }

}
