<?php

namespace App\Scopes\Filters;

trait ValueFilter
{
    // Фильтрация по городу
    public function scopeValueFilter($query, $request, $name_column)
    {

        //Фильтруем временному интервалу
        if(isset($request->$name_column)){
           	$query = $query->where($name_column, $request->$name_column);
        };
        return $query;
    }

}
