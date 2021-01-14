<?php

namespace App\Http\Controllers\Project;

use App\Models\Project\PricesGoods;

class PricesGoodsController extends BaseController
{
    /**
     * PricesGoodsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO - 19.02.20 - Статичный метод для виан дизеля
        $site = $this->site;
        $page = $site->pages_public->where('alias', 'prices-goods')->first();

        $prices_goods = PricesGoods::with([
            'goods.article' => function ($q) {
                $q->with([
                    'photo',
                    'manufacturer.company',
                ]);
            },
            'currency'
        ])
            ->has('goods')
            ->whereHas('catalog', function ($q) use ($site) {
                $q->whereHas('filials', function ($q) use ($site) {
                    $q->where('id', $site->filial->id);
                });
            })
            ->where([
                'display' => true,
                'archive' => false,
            ])
            ->get();

        return view($site->alias . '.pages.prices_goods.index', compact('site', 'page', 'prices_goods'));
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $site = $this->site;
        $page = $site->pages_public->firstWhere('alias', 'prices-goods');

        $price_goods = PricesGoods::with([
            'goods.article.raws',
            'currency',
            'catalog'
        ])
            ->where([
                'display' => true,
                'archive' => false
            ])
            ->find($id);

        if (empty($price_goods) || $price_goods->catalog->is_access_page == 0) {
            abort(404, $site->alias);
        }

        $page->title = $price_goods->goods->article->name;

        return view("{$site->alias}.pages.prices_goods.index", compact('site', 'page', 'price_goods'));
    }

    /**
     * Поиск прайсов по имени, внешнему артикулу
     *
     * @param $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($search)
    {

        $items = PricesGoods::with([
            'catalogs_item' => function ($q) {
                $q->with([
                    'directive_category:id,alias',
                    'parent'
                ]);
            },
            'catalog'
        ])
            ->where([
                'archive' => false,
                'company_id' => $this->site->company_id,
                'filial_id' => $this->site->filial->id,
                'display' => true,
            ])
            // TODO - 17.06.20 - Нужна проверка на display у всех родителей раздела (если они есть), видимо рекусия
            ->whereHas('catalogs_item', function ($q) {
                $q
//                    ->whereHas('parent', function ($q) {
//                    $q->where('display', true);
//                })
                    ->where('display', true);
            })
            ->whereHas('catalog', function ($q) {
                $q->where('display', true);
            })
            ->whereHas('goods', function ($q) use ($search) {
                $q->whereHas('article', function ($q) use ($search) {
                    $q->where([
                        'draft' => false,
                        'display' => true,
                    ])
                        ->where(function ($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('external', 'LIKE', '%' . $search . '%')
                                ->orWhereHas('codes', function ($q) use ($search) {
                                    $q->where('name', 'LIKE', '%' . $search . '%');
                                });
                        });
                })
                    ->where([
                        'archive' => false,
                        'display' => true,
                    ]);
            })
            ->get();
//        dd($items);

        return response()->json($items);
    }
}
