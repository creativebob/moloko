<?php

namespace App\Scopes\Filters;

trait PositionFilter
{
    // Фильтрация по городу
    public function scopePositionFilter($query, $request)
    {

        //Фильтруем по списку городов
        if($request->position_id){
          $query = $query->whereIn('position_id', $request->position_id);
        };

      return $query;
    }

}
