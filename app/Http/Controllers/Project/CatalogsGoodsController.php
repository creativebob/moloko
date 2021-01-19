<?php

namespace App\Http\Controllers\Project;

use App\Models\Project\CatalogsGoods;

class CatalogsGoodsController extends BaseController
{
    /**
     * CatalogsGoodsController constructor.
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
    public function show($slug)
    {
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'catalogs-goods')->first();

        // Получаем полный прайс со всеми доступными разделами
        $catalog_goods = CatalogsGoods::where('slug', $slug)
            ->whereHas('filials', function ($q) use ($site) {
                $q->where('id', $site->filial->id);
            })
            ->display()
            ->first();
//        dd($catalog_goods);
        if (empty($catalog_goods)) {
            abort(404);
        }

        return view($site->alias.'.pages.catalogs_goods.index', compact('site',  'page', 'catalog_goods'));
    }
}
