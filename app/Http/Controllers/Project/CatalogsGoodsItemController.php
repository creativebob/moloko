<?php

namespace App\Http\Controllers\Project;

use App\CatalogsGoodsItem;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Project\Traits\Commonable;
use Illuminate\Http\Request;

class CatalogsGoodsItemController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        // TODO - 19.02.20 - Решение для простотра раздела каталога
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'catalogs-goods-item')->first();

        // Получаем полный прайс со всеми доступными разделами
        $catalogs_goods_item = CatalogsGoodsItem::with([
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
        if ($catalogs_goods_item) {
            return view($site->alias.'.pages.catalogs_goods_item.index', compact('site',  'page', 'request', 'catalogs_goods_item'));
        } else {
            abort(403, 'Доступ к прайсу товаров компании ограничен. Согласен, это довольно странно...');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
