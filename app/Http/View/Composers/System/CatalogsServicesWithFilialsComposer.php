<?php

namespace App\Http\View\Composers\System;

use App\CatalogsService;

use Illuminate\View\View;

class CatalogsServicesWithFilialsComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('catalogs_services', false, 'index');

        // TODO - 25.12.19 - Нужна разбивка каталогов по филиалау пользователя

        // Главный запрос
        $catalogsServices = CatalogsService::with([
            'items' => function ($q) {
                $q->orderBy('sort');
            },
            'filials'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->get();
        // dd($catalogsServices);

//        $catalogsServicesItems = [];
//        $filials = [];
        foreach($catalogsServices as $catalogServices) {

            $catalogServices->items_tree = buildTreeArray($catalogServices->items);

//            $catalogsServicesItems[$catalogServices->id] = buildTreeArray($catalogServices->items);


//            foreach($catalogServices->filials as $filial) {
//                $filial->catalogs_goods_id = $catalogServices->id;
//                $filials[] = $filial;
//            }
        }
//        dd($catalogsServicesItems);

        $currencies = auth()->user()->company->currencies;

        $catalogsData = [
            'catalogs' => $catalogsServices,
//            'catalogsItems' => $catalogsServicesItems,
//            'filials' => $filials,
            'currencies' => $currencies
        ];
//        dd($catalogsData);

        return $view->with(compact('catalogsData'));
    }

}
