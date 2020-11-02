<?php

namespace App\Http\Controllers\Project;

use App\Goods;
use Illuminate\Http\Request;

class FavoritesGoodsController extends BaseController
{
    /**
     * FavoritesGoodsController constructor.
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

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'favorites_goods')
            ->first();

        $filialId = $site->filial->id;

        $user = auth()->user()->load([
            'favoritesGoods' => function($q) use ($filialId) {
                $q->whereHas('prices', function($q) use ($filialId) {
                    $q->where('filial_id', $filialId);
                })
                ->with([
                    'prices' => function($q) use ($filialId) {
                        $q->where('filial_id', $filialId);
                    }
                ]);
           }
        ]);
//        dd($user);

        $favoritesPricesGoods = [];
        foreach($user->favoritesGoods as $favoriteGoods) {
            $favoritesPricesGoods[] = $favoriteGoods->prices->first();
        }
//        dd($favoritesPricesGoods);

        return view("{$site->alias}.pages.favorites_goods.index", compact('site',  'page', 'favoritesPricesGoods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        auth()->user()->favoritesGoods()->attach($request->goods_id);

        $curGoods = Goods::with([
            'article',
            'metrics'
        ])
            ->find($request->goods_id);

        return response()->json([
            'success' => true,
            'cur_goods' => $curGoods
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        auth()->user()->favoritesGoods()->detach($id);
        return response()->json(['success' => true]);
    }
}
