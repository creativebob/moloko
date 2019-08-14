<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

// Куки
use Illuminate\Support\Facades\Cookie;

class CitiesComposer
{
	public function compose(View $view)
	{

        $site = $view->site->load('company.filials.location.city');

        $filials = $site->company->filials->where('display', true)->unique('location.city_id');
        // dd($filials);

        $cities_list = [];
        $active_city = null;

        foreach ($filials as $filial) {
        	$cities_list[] = $filial->location->city;

        	if ($filial->location->city_id == Cookie::get('city_id')) {
        		$active_city = $filial->location->city;
        	}
        }

        $cities = [];
        $cities['cities_list'] = $cities_list;
        $cities['active_city'] = $active_city;
        // dd($cities);

        return $view->with('cities', $cities);
    }

}