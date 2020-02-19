<?php

namespace App\Http\View\Composers\Project;

use Illuminate\View\View;

class CatalogsItemsComposer
{
	public function compose(View $view)
	{

        $catalogs_items = $view->catalogs_items->where('display', true);
        // dd($catalogs_items);

        $catalogs_items = buildTree($catalogs_items);
        // dd($catalogs_items);

        return $view->with('catalogs_items', $catalogs_items);
    }

}
