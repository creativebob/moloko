<?php

namespace App\Scopes\Filters;

trait StatusFilter
{

    // Фильтрация по секторам
    public function scopeStatusFilter($query, $request)
    {

        //Фильтруем по секторам
        if($request->status_result){
          $query = $query->where('status', $request->status);
        };

      return $query;
    }

}
