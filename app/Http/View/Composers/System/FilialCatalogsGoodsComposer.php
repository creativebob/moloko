<?php

namespace App\Http\View\Composers\System;

use App\CatalogsGoods;

use Illuminate\View\View;

class FilialCatalogsGoodsComposer
{
	public function compose(View $view)
	{
        $filialId = $view->filialId;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_goods', false, 'index');

        // TODO - 25.12.19 - Нужна разбивка каталогово по филиалау пользователя

        // Главный запрос
        $catalogsGoods = CatalogsGoods::moderatorLimit($answer)
        ->companiesLimit($answer)
            ->whereHas('filials', function ($q) use ($filialId) {
                $q->where('id', $filialId);
            })
        ->toBase()
        ->get();
        // dd($catalogs_goods);

        return $view->with(compact('catalogsGoods'));
    }

}
