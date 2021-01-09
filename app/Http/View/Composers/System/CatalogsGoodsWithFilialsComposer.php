<?php

namespace App\Http\View\Composers\System;

use App\CatalogsGoods;

use Illuminate\View\View;

class CatalogsGoodsWithFilialsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_goods', false, 'index');

        // TODO - 25.12.19 - Нужна разбивка каталогов по филиалау пользователя

        // Главный запрос
        $catalogsGoods = CatalogsGoods::with([
            'items' => function ($q) {
                $q->orderBy('sort');
            },
            'filials'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();
        // dd($catalogs_goods);

//        $catalogsGoodsItems = [];
//        $filials = [];
        foreach($catalogsGoods as $catalogGoods) {

            $catalogGoods->items_tree = buildTreeArray($catalogGoods->items);

//            $catalogsGoodsItems[$catalogGoods->id] = buildTreeArray($catalogGoods->items);


//            foreach($catalogGoods->filials as $filial) {
//                $filial->catalogs_goods_id = $catalogGoods->id;
//                $filials[] = $filial;
//            }
        }
//        dd($catalogsGoodsItems);

        $currencies = auth()->user()->company->currencies;

        $catalogsData = [
            'catalogs' => $catalogsGoods,
//            'catalogsItems' => $catalogsGoodsItems,
//            'filials' => $filials,
            'currencies' => $currencies
        ];
//        dd($catalogsData);

        return $view->with(compact('catalogsData'));
    }

}
