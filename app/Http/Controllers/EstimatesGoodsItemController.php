<?php

namespace App\Http\Controllers;

use App\EstimatesGoodsItem;
use App\PricesGoods;
use Illuminate\Http\Request;

class EstimatesGoodsItemController extends Controller
{

    // Настройки сконтроллера
    public function __construct(EstimatesGoodsItem $estimates_goods_item)
    {
        $this->middleware('auth');
        $this->estimate = $estimates_goods_item;
        $this->class = EstimatesGoodsItem::class;
        $this->model = 'App\EstimatesGoodsItem';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

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

        $prices_goods = PricesGoods::findOrFail($request->price_id);
        $prices_goods->load('product');

        $estimates_goods_item = EstimatesGoodsItem::create([
            'estimate_id' => $request->estimate_id,
            'goods_id' => $prices_goods->product->id,
            'price_id' => $prices_goods->id,
            'price' => $prices_goods->price,
            'count' => 1,
            'amount' => $prices_goods->price
        ]);


        $estimates_goods_item->load('product.article');
        return view('leads.estimate.estimates_goods_item', compact('estimates_goods_item'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EstimatesGoodsItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EstimatesGoodsItem  $estimatesItem
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
     * @param  \App\EstimatesGoodsItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {

        $estimates_goods_item = EstimatesGoodsItem::findOrFail($id);

        $count = $estimates_goods_item->count + 1;
        $amount = $count * $estimates_goods_item->price;

        $estimates_goods_item->update([
            'count' => $count,
            'amount' => $amount
        ]);

        // dd($estimates_goods_item);

        $estimates_goods_item->load('product.article');
        return view('leads.estimate.estimates_goods_item', compact('estimates_goods_item'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EstimatesGoodsItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = EstimatesGoodsItem::destroy($id);
        return response()->json($result);
    }


//    public function ajax_edit(Request $request)
//    {
//
//        // Получаем авторизованного пользователя
//        $user = $request->user();
//
//        $user_id = hideGod($user);
//        $company_id = $user->company_id;
//
//        $estimate_item = EstimatesGoodsItem::findOrFail($request->id);
//
//        return view('leads.pricing.pricing-modal', compact('estimate_item'));
//
//    }
}
