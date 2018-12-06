<?php

namespace App\Scopes\Filters;

trait BooleanFilter
{
    // Фильтрация по городу
    public function scopeBooleanFilter($query, $request, $name_column)
    {

        //Фильтруем временному интервалу
        if(isset($request->$name_column)){

        	if($request->$name_column == 0){
           		$query = $query->where($name_column, 0);
        	}

        	if($request->$name_column == 1){
           		$query = $query->where($name_column, '!=', 0);
        	}

        	if($request->$name_column == 2){
        		return $query;
        	}

        	return $query;
        };

    }

}
