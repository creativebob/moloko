<?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

class CatalogsItemsComposer
{
	public function compose(View $view)
	{

        $catalogs_items = $view->catalogs_items->where('display', 1);
        // dd($catalogs_items);

        $catalogs_items = buildTree($catalogs_items);
        // dd($catalogs_items);

        return $view->with('catalogs_items', $catalogs_items);
    }

}