<?php

namespace App\Scopes\Traits;

trait AuthorsTraitScopes
{

        // Фильтрация для показа авторов
    public function scopeAuthors($query, $authors)
    {

        if(isset($authors)){

            if($authors['authors_id'] == null){

                // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                return $query->Where('author_id', $authors['user_id'])->orWhere('id', $authors['user_id']);

            } else {

              // $authors['authors_id'] = collect($authors['authors_id'])->implode(', ');
              // // dd($authors['authors_id']);

                // // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
                return $query->WhereIn('author_id', $authors['authors_id'])->orWhere('author_id', $authors['user_id'])->orWhere('id', $authors['user_id']);

                // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя

                // dd($filials);
                // return $query->whereHas('staff', function ($query) use ($filials){
                //   $query->whereIn('filial_id', $filials);
                // })->WhereIn('author_id', $authors['authors_id'])->orWhere('author_id', $authors['user_id'])->orWhere('id', $authors['user_id']);
            };

        } else {

            // Без ограничений
            // dd($authors['authors_id']);
             return $query;
        }
    }
}
