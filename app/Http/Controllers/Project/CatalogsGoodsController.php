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

        // Проверим, а доступен ли каталог товаров. Если нет, то кидаем ошибку
        if ($catalog_goods) {
            return view($site->alias.'.pages.catalogs_goods.index', compact('site',  'page', 'catalog_goods'));
        } else {
            abort(403, 'Доступ к прайсу товаров компании ограничен. Согласен, это довольно странно...');
        }
    }
}
