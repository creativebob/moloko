<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use App\Models\Project\PricesGoods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PricesGoodsController extends Controller
{

    use Commonable;

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
            'goods_public.article' => function ($q) {
                $q->with([
                    'photo',
                    'manufacturer.company',
                ]);
            },
            'currency'
        ])
            ->has('goods_public')
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

        return view($site->alias.'.pages.prices_goods.index', compact('site',  'page', 'prices_goods'));
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
    public function show($id)
    {

        $site = $this->site;

        $page = $site->pages_public->where('alias', 'prices-goods')->first();

        $price_goods = PricesGoods::with([
            'goods_public.article.raws',
            'currency'
        ])
            ->where([
                'display' => true
            ])
            ->findOrFail($id);

        // dd($price_goods->goods_public->article->containers);

        $page->title = $price_goods->goods_public->article->name;

        return view($site->alias.'.pages.prices_goods.index', compact('site',  'page', 'price_goods'));
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

    public function search($search)
    {

        $items = PricesGoods::with([
            'goods_public' => function ($q) {
                $q->with([
                    'article.photo',
                    'metrics.values'
                ]);

            },
            'currency',
            'catalogs_item.directive_category:id,alias',
            'catalogs_item.parent'
        ])
            ->where([
                'archive' => false,
                'company_id' => $this->site->company_id,
                'filial_id' => $this->site->filial->id,
                'display' => true,
            ])
            ->whereHas('goods_public', function($q) use ($search) {
                $q->whereHas('article', function ($q) use ($search) {
                    $q->where([
                        'draft' => false,
                        'display' => true,
                    ])
                        ->where(function ($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('external', 'LIKE', '%' . $search . '%');
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
