<?php

namespace App\Scopes\Filters;
use Carbon\Carbon;

trait DateIntervalFilter
{
    // Фильтрация по городу
    public function scopeDateIntervalFilter($query, $request, $name_column)
    {

        if(isset($request->date_start)){$date_start = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_start . '00:00:00');};
        if(isset($request->date_end)){$date_end = Carbon::createFromFormat('d.m.Y H:i:s', $request->date_end . '23:59:59');};

        //Фильтруем временному интервалу: если указана только начальная дата
        if(($request->date_start)&&(!isset($request->date_end))){
            $query = $query->where($name_column, '>', $date_start);
        };

        //Фильтруем временному интервалу: если указана только конечная дата
        if(($request->date_end)&&(!isset($request->date_start))){
            $query = $query->where($name_column, '<', $date_end);
        };

        //Фильтруем временному интервалу
        if(($request->date_start)&&($request->date_end)){
           	$query = $query->whereBetween($name_column, [$date_start, $date_end]);
        };

        return $query;
    }

}
