<?php

namespace App\Http\View\Composers\Project;

use App\CatalogsService;
use Illuminate\View\View;

class CatalogsServiceComposer
{
	public function compose(View $view)
	{

        $site = $view->site;

        $catalog_services = CatalogsService::with([
            'items' => function ($q) {
                $q->withCount('prices_public')
                    ->where('display', true)
                    ->orderBy('sort');
            }
        ])
            ->whereHas('filials', function($q) use ($site) {
                $q->where('id', $site->filial->id);
            })
            ->where([
                'display' => true
            ])
            ->orderBy('sort')
            ->first();
//        dd($catalog_services);

        return $view->with(compact('catalog_services'));
    }

}
