<?php

namespace App\Scopes\Filters;

trait AuthorFilter
{

    // Фильтрация по секторам
    public function scopeAuthorFilter($query, $request)
    {

        //Фильтруем по секторам
        if($request->author_id){
          $query = $query->whereIn('author_id', $request->author_id);
        };

      return $query;
    }

}
