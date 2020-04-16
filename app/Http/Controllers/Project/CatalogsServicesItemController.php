<?php

namespace App\Http\Controllers\Project;

use App\CatalogsServicesItem;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Project\Traits\Commonable;
use App\Models\Project\PricesService;
use Illuminate\Http\Request;

class CatalogsServicesItemController extends Controller
{
    use Commonable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $catalog_slug, $slug)
    {
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'catalogs-services-items')->first();

        // Получаем полный прайс со всеми доступными разделами
        $catalogs_services_item = CatalogsServicesItem::with([
            'catalog',
            'prices' => function ($q) use ($site) {
                $q->with([
                    'service_public' => function ($q) {
                        $q->with([
                            'process' => function ($q) {
                                $q->with([
                                    'photo',
                                    'unit_length',
                                    'positions.actual_staff.user.photo'
                                ]);
                            },
                            'metrics',
                        ]);
                    },
                    'currency',
                ])
                    ->has('service_public')
                    ->where([
                        'display' => true,
                        'archive' => false,
                        'filial_id' => $site->filial->id
                    ])
                    ->orderBy('sort', 'asc');
            },
//            'directive_category:id,alias',
//            'display_mode',
//            'filters.values'
        ])
            ->whereHas('catalog', function ($q) use ($site, $catalog_slug) {
                $q->where('slug', $catalog_slug)
                    ->whereHas('filials', function ($q) use ($site) {
                        $q->where('id', $site->filial->id);
                    });
            })
            ->where('slug', $slug)
            ->where([
                'display' => true
            ])
            ->first();

        // Проверим, а доступен ли каталог товаров. Если нет, то кидаем ошибку
        if ($catalogs_services_item) {
            return view($site->alias.'.pages.catalogs_services_items.index', compact('site',  'page', 'request', 'catalogs_services_item'));
        } else {
            abort(403, 'Доступ к прайсу товаров компании ограничен. Согласен, это довольно странно...');
        }
    }
}
