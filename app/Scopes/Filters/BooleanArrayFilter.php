<?php

namespace App\Scopes\Filters;

trait BooleanArrayFilter
{
    // Фильтрация по городу
    public function scopeBooleanArrayFilter($query, $request, $name_column)
    {

        if($request->$name_column != null){

            $zero = in_array('0', $request->$name_column);
            $one = in_array('1', $request->$name_column);

            if($zero && $one){
                return $query;
            }
            elseif(!$zero && $one){
                $query = $query->where($name_column, '!=', 0);
            }
            elseif($zero && !$one){
                $query = $query->where($name_column, 0);
            }
        	return $query;
        }

    }

}
