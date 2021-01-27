<?php

namespace App\Http\View\Composers\Project;

use App\Models\Project\Album;
use Illuminate\View\View;

class AlbumByAliasComposer
{
	public function compose(View $view)
	{
	    $site = $view->site;

        $album = Album::with([
            'photos' => function ($q) {
                $q->where('display', true)
                ->oldest('sort');
            }
        ])
            ->where('display', true)
            ->where('alias', $view->albumAlias)
            ->where('company_id', $site->company_id)
            ->first();
//        dd($album);

        return $view->with(compact('album'));
    }

}
