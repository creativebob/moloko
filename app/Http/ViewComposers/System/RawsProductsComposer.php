<?php

namespace App\Http\ViewComposers\System;

use App\RawsProduct;

use Illuminate\View\View;

class RawsProductsComposer
{
	public function compose(View $view)
	{

        if (isset($view->raws_products)) {
            return $view->with('raws_products', $view->raws_products);
        } else {
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_raws_products = operator_right('raws_products', false, 'index');

            // Группы товаров
            $raws_products = RawsProduct::where(['raws_category_id' => $view->raws_category_id, 'set_status' => $view->set_status])
            ->orderBy('sort', 'asc')
            ->get(['id', 'name']);
            // dd($raws_products);

            return $view->with('raws_products', $raws_products);
        }
    }

}