<?php

namespace App\Scopes\Filters;

trait CityFilter
{
    // Фильтрация по городу
    public function scopeCityFilter($query, $request, $relations = null)
    {

        // Проверка на существование
        if($request->city_id){


            // ПРЯМОЙ ЗАПРОС
            // --------------------------------------------------------------------------------------------------------------------------------------
            if($relations == null){



                if($request->city_id == [0 => 'null']){

                    $query = $query->WhereNull('city_id');
                } else 
                {
                    $query = $query->whereIn('city_id', $request->city_id)->orWhereNull('city_id');
                };

            // ЗАПРОС ЧЕРЕЗ СВЯЗЬ
            // --------------------------------------------------------------------------------------------------------------------------------------
            } else {

                // Если приходит массив только с нулем (Требуеться выбрать только записи без города)
                if($request->city_id == [0 => 'null']){

                    $query = $query->WhereNull('location_id');
                } else 
                {

                    // Если приходит массив с нулем и другими ID
                    if(in_array('null', $request->city_id)){

                        $query = $query->whereHas('location', function ($query) use ($request) {
                            $query = $query->whereIn('city_id', $request->city_id);
                        })->orWhereNull('location_id');

                    // Если массив приходит без нуля
                    } else {


                        $query = $query->whereHas('location', function ($query) use ($request) {
                            $query = $query->whereIn('city_id', $request->city_id);
                        });
                    };

                };

            };
        };

        return $query;
    }

}
