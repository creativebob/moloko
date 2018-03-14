<?php

namespace App\Scopes\Filters;
use App\Booklist;
use App\List_item;

trait BooklistFilter
{
    // Фильтрация по городу
    public function scopeBooklistFilter($query, $request)
    {

        //Фильтруем по списку городов
        if($request->booklist_id){

		    $items_booklists = List_item::whereIn('booklist_id', $request->booklist_id)->get()->keyBy('item_entity')->keys()->toArray();
		    
       		// $user_booklists = Auth::user()->booklists_author->where('entity_alias', $this->entity_name)->keyBy('id')->keys();

			$query = $query->whereIn('id', $items_booklists);

        };

      return $query;
    }

}
