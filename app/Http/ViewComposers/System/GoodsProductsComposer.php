<?php

namespace App\Http\ViewComposers\System;

use App\GoodsProduct;

use Illuminate\View\View;

class GoodsProductsComposer
{
	public function compose(View $view)
	{

        if (isset($view->goods_products)) {
            return $view->with('goods_products', $view->goods_products);
        } else {
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer_goods_products = operator_right('goods_products', false, 'index');

            // Группы товаров
            $goods_products = GoodsProduct::where(['goods_category_id' => $view->goods_category_id, 'set_status' => $view->set_status])
            ->companiesLimit($answer)
            ->orderBy('sort', 'asc')
            ->get(['id', 'name']);
            // dd($goods_products);

            return $view->with('goods_products', $goods_products);
        }
    }

}