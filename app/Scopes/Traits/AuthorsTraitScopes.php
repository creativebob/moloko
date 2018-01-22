<?php

namespace App\Scopes\Traits;

trait AuthorsTraitScopes
{

        // Фильтрация для показа авторов
    public function scopeAuthors($query, $authors)
    {

        if(isset($authors)){
            if($authors['authors_id'] == null){

                    $user_id = $authors['user_id'];

                    return $query
                    ->Where(function ($query) use ($user_id, $authors) {$query
                    ->orWhere('author_id', $user_id)
                    ->orWhere('id', $user_id);});

            } else {

                    $user_id = $authors['user_id'];
                    $authors = $authors['authors_id'];

                    return $query
                    ->Where(function ($query) use ($user_id, $authors) {$query
                    ->orWhere('author_id', $user_id)
                    ->orWhereIn('author_id', $authors)
                    ->orWhere('id', $user_id);});
            };

        } else {

                    $user_id = $authors['user_id'];

                    return $query
                    ->Where(function ($query) use ($user_id, $authors) {$query
                    ->orWhere('author_id', $user_id)
                    ->orWhere('id', $user_id);});
        }
    }
}
