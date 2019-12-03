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
    public function index($url)
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
    public function show($id)
    {

        $site = $this->site;
        $filial = $this->filial;
        $page = $site->pages_public->where('alias', 'prices-goods')->first();

        $price_goods = PricesGoods::with([
            'goods_public.article.raws'
        ])
            ->where([
                'display' => true
            ])
            ->findOrFail($id);

        // dd($price_goods->goods_public->article->containers);

        $page->title = $price_goods->goods_public->article->name;

        return view($site->alias.'.pages.prices_goods.index', compact('site', 'filial', 'page', 'price_goods'));
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
            }
        ])
            ->where([
                'archive' => false,
                'company_id' => $this->site->company_id,
                'filial_id' => $this->filial->id,
            ])
            ->whereHas('goods_public', function($q) use ($search) {
                $q->whereHas('article', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->where('draft', false);;
                })
                ->where('archive', false);
            })
            ->get();

//        dd($items);

        return response()->json($items);
    }
}
