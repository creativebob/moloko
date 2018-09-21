<?php

namespace App\Scopes\Filters;

trait DateIntervalFilter
{
    // Фильтрация по городу
    public function scopeDateIntervalFilter($query, $request, $name_column)
    {

        //Фильтруем временному интервалу: если указана только начальная дата
        if(($request->date_start)&&(!isset($request->date_end))){
            $date_start = date_to_mysql($request->date_start);
            $query = $query->where($name_column, '>', $date_start);
        };

        //Фильтруем временному интервалу: если указана только конечная дата
        if(($request->date_end)&&(!isset($request->date_start))){
            $date_end = date_to_mysql($request->date_end);
            $query = $query->where($name_column, '<', $date_end);
        };

        //Фильтруем временному интервалу
        if(($request->date_start)&&($request->date_end)){
      			$date_start = date_to_mysql($request->date_start);
      			$date_end = date_to_mysql($request->date_end);
           	$query = $query->whereBetween($name_column, [$date_start, $date_end]);
        };

      return $query;
    }

}
