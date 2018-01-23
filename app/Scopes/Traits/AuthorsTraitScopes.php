<?php

namespace App\Scopes\Traits;

trait AuthorsTraitScopes
{

        // Фильтрация для показа авторов
    public function scopeAuthors($query, $all_authors)
    {
        $user_id = $all_authors['user_id'];

        if($all_authors['authors_status']){

            if($all_authors['list_authors'] == null){

                    return $query;

            } else {

                    $authors = $all_authors['list_authors'];

                    return $query
                    ->Where(function ($query) use ($user_id, $authors) {$query
                    ->orWhere('author_id', $user_id)
                    ->orWhereIn('author_id', $authors);});
            };

        } else {

            return $query->Where('author_id', $user_id);
        }
    }
}
