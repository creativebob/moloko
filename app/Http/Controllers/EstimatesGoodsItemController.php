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

        $price_goods = PricesGoods::findOrFail($request->price_id);
        $price_goods->load('product');

        if ($price_goods->product->serial == 1) {
            $estimates_goods_item = EstimatesGoodsItem::create([
                'estimate_id' => $request->estimate_id,
                'goods_id' => $price_goods->product->id,
                'price_id' => $price_goods->id,
                'price' => $price_goods->price,
                'count' => 1,
                'amount' => $price_goods->price
            ]);
        } else {

            // TODO - 28.10.19 - Проверка при добавлении при множественном клике в смете, уйдет с Vue

            $estimates_goods_item = EstimatesGoodsItem::firstOrNew([
                'estimate_id' => $request->estimate_id,
                'goods_id' => $price_goods->product->id,
                'price_id' => $price_goods->id,
            ], [
                'price' => $price_goods->price,
                'count' => 1,
                'amount' => $price_goods->price
            ]);

            if ($estimates_goods_item->id) {

                $count = $estimates_goods_item->count + 1;
                $amount = $count * $estimates_goods_item->price;

                $estimates_goods_item->update([
                    'count' => $count,
                    'amount' => $amount
                ]);

            } else {
                $estimates_goods_item->save();
            }
        }

        $estimates_goods_item->load('product.article');
        $this->estimateUpdate($estimates_goods_item);

        return response()->json($estimates_goods_item);
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
    public function update(Request $request, $id)
    {

        $estimates_goods_item = EstimatesGoodsItem::findOrFail($id);
        // dd($estimates_goods_item);

        $result = $estimates_goods_item->update([
            'count' => $request->count,
        ]);
//        dd($result);

        $estimates_goods_item->load('product.article');
	    $this->estimateUpdate($estimates_goods_item);

        return response()->json($estimates_goods_item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EstimatesGoodsItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
	    $estimates_goods_item = EstimatesGoodsItem::findOrFail($id);
	    
	    $result = $estimates_goods_item->delete();
	
	    $this->estimateUpdate($estimates_goods_item);
//        $result = EstimatesGoodsItem::destroy($id);
        return response()->json($result);
    }
	
	public function estimateUpdate($item)
	{
		$estimate = $item->estimate;
		$estimate->load('goods_items');
		
		if ($estimate->goods_items->isNotEmpty()) {
			
			$amount = $estimate->goods_items->sum('amount');
			$discount = (($amount * $estimate->discount_percent) / 100);
			$total = ($amount - $discount);
			
			$data = [
				'amount' => $amount,
				'discount' => $discount,
				'total' => $total
			];
		} else {
			$data = [
				'amount' => 0,
				'discount' => 0,
				'total' => 0
			];
		}
		
		$estimate->update($data);

//		return $estimate;
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
