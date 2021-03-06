<?php

namespace App\Http\View\Composers\System;

use App\City;

use Illuminate\View\View;

class CitySearchComposer
{
	public function compose(View $view)
	{
        $item = $view->item;

        $prefix = $view->prefix ?? null;
        $relation = $prefix ? "{$prefix}_location" : 'location';
        $item->load([
            $relation => function ($q) {
                $q->with('city:id,name');
            }
            ]);
        $location = $item->$relation;

        if (is_null($location)) {
            if (auth()->user()->location) {
                $city = auth()->user()->location->city;
            } else {
                $city = collect((object) [
                    'id' => null,
                    'name' => null
                ]);
            }
        } else {
            $city = $location->city;
        }

//        dd($city);

        return $view->with(compact('city'));
    }

}
