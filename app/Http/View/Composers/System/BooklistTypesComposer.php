<?php

namespace App\Http\View\Composers\System;

use App\BooklistType;

use Illuminate\View\View;

class BooklistTypesComposer
{
	public function compose(View $view)
	{

		$entity_alias = $view->entity_alias;

        // Главный запрос
        $booklist_types = BooklistType::when(extra_right('booklist-area-manager'), function ($query) use ($entity_alias){
            return $query->where('tag', 'booklist-area-manager')->where('entity_alias', $entity_alias);
        })
        ->orWhere('tag', 'simple')
        ->orderBy('sort', 'asc')
        ->get();

        return $view->with('booklist_types', $booklist_types);
    }

}
