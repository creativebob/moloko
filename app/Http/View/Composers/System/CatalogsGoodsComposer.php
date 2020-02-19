<?php

namespace App\Http\View\Composers\System;

use App\CatalogsGoods;

use Illuminate\View\View;

class CatalogsGoodsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_goods', false, 'index');

        // TODO - 25.12.19 - Нужна разбивка каталогово по филиалау пользователя

        // Главный запрос
        $catalogs_goods = CatalogsGoods::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->toBase()
        ->get();
        // dd($catalogs_goods);

        return $view->with(compact('catalogs_goods'));
    }

}
