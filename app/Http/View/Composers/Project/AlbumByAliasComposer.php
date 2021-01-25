<?php

namespace App\Http\View\Composers\Project;

use App\Models\Project\Album;
use Illuminate\View\View;

class AlbumByAliasComposer
{
	public function compose(View $view)
	{
        $album = Album::with([
            'photos'
        ])
            ->where('alias', $view->albumAlias)
            ->first();
//        dd($album);

        return $view->with(compact('album'));
    }

}
