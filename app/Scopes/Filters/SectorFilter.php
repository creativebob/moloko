<?php

namespace App\Scopes\Filters;

trait SectorFilter
{

    // Фильтрация по секторам
    public function scopeSectorFilter($query, $request)
    {

        //Фильтруем по секторам
        if($request->sector_id){
          $query = $query->whereIn('sector_id', $request->sector_id);
        };

      return $query;
    }

}
