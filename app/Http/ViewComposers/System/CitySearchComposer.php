<?php

namespace App\Http\ViewComposers\System;

use App\City;

use Illuminate\View\View;

class CitySearchComposer
{
	public function compose(View $view)
	{
//        $cities = City::with([
//            'area:id,name',
//            'region:id,name',
//            'country:id,name'
//        ])
//            ->get([
//                'id',
//                'name',
//                'area_id',
//                'region_id',
//                'country_id'
//            ]);
//         dd($cities);

        $item = $view->item;
        $item->load([
            'location' => function ($q) {
                $q->with('city:id,name');
            }
            ]);
        $location = $item->location;

        if (is_null($location)) {
            $city = \Auth::user()->location->city;
//            $city = collect((object) [
//                'id' => null
//            ]);
        } else {
            $city = $location->city;
        }
//        dd($city);


//        $required = is_null($view->required) ? false : true;
//        dd($required);

        return $view->with(compact('city'));
    }

}
