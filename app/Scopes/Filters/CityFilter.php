<?php

namespace App\Scopes\Filters;

trait CityFilter
{
    // Фильтрация по городу
    public function scopeCityFilter($query, $request)
    {

        //Фильтруем по списку городов
        // if($request->city_id){
        //   $query = $query->whereIn('city_id', $request->city_id);
        // };

        if($request->city_id){

			$query = $query->whereHas('location', function ($query) use ($request) {
			    $query = $query->whereIn('city_id', $request->city_id);
			})->orWhereNull('location_id');
	    };

    	return $query;
    }

}
