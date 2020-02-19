<?php

namespace App\Http\View\Composers\System;

use App\City;

use Illuminate\View\View;

class CitiesComposer
{
	public function compose(View $view)
	{
        $cities = City::with([
            'area:id,name',
            'region:id,name',
            'country:id,name'
        ])
            ->get([
                'id',
                'name',
                'area_id',
                'region_id',
                'country_id'
            ]);
//         dd($cities);

        return $view->with(compact('cities'));
    }

}
