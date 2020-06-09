<?php

namespace App\Http\View\Composers\System;

use App\City;
use App\Client;
use Illuminate\View\View;

class ClientsCitiesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
//        $answer = operator_right('clients', false, getmethod('index'));
//
//        $clients = Client::with([
//            'clientable.location.city',
//        ])
//            ->companiesLimit($answer)
//            ->moderatorLimit($answer)
//            ->authors($answer)
//            ->systemItem($answer)
//            ->whereNotNull('first_order_date')
//            ->get();
////         dd($cities);
//
//        $cities = $clients->unique('clientable.location.city');
//        dd($cities);

        $cities = City::get();

        return $view->with(compact('cities'));
    }

}
