<?php

namespace App\Http\ViewComposers;

use App\CatalogsGoods;

use Illuminate\View\View;

class CatalogsGoodsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_goods', false, 'index');

        $catalogs_type = $view->type;

        // Главный запрос
        $catalogs_goods = CatalogsGoods::with('items')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();

        return $view->with(compact('catalogs_goods'));
    }

}
