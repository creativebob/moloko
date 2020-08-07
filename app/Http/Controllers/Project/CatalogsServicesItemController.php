<?php

namespace App\Http\Controllers\Project;

use App\Models\Project\CatalogsServicesItem;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Project\Traits\Commonable;
use Illuminate\Http\Request;

class CatalogsServicesItemController extends Controller
{
    use Commonable;

    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Отображение указанного ресурса.
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
            'filters.values'
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

        // Проверим, а доступен ли каталог товаров. Если нет, то кидаем ошибку
        if ($catalogs_services_item) {
            return view($site->alias.'.pages.catalogs_services_items.index', compact('site',  'page', 'request', 'catalogs_services_item'));
        } else {
            abort(403, 'Доступ к прайсу товаров компании ограничен. Согласен, это довольно странно...');
        }
    }
}
