<?php

namespace App\Http\Controllers\Project;

use App\Models\Project\CatalogsServicesItem;

class CatalogsServicesItemController extends BaseController
{
    /**
     * CatalogsServicesItemController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function show($catalog_slug, $slug)
    {
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'catalogs-services-items')->first();

        // Получаем полный прайс со всеми доступными разделами
        $catalogs_services_item = CatalogsServicesItem::with([
            'catalog',
            'prices',
            'filters.values',
            'seo' => function ($q) {
                $q->withCount('childs');
            }
        ])
            ->where('slug', $slug)
            ->whereHas('catalog', function ($q) use ($site, $catalog_slug) {
                $q->where('slug', $catalog_slug)
                    ->whereHas('filials', function ($q) use ($site) {
                        $q->where('id', $site->filial->id);
                    });
            })
            ->display()
            ->first();
//        dd($catalogs_services_item);
        if (empty($catalogs_services_item)) {
            abort(404);
        }

        return view($site->alias.'.pages.catalogs_services_items.index', compact('site',  'page', 'request', 'catalogs_services_item'));
    }
}
