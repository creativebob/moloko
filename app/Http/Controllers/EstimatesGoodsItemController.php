<?php

namespace App\Http\Controllers;

use App\Estimate;
use App\EstimatesGoodsItem;
use App\Stock;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\Reservable;
use App\PricesGoods;
use Illuminate\Http\Request;

class EstimatesGoodsItemController extends Controller
{

    /**
     * EstimatesGoodsItemController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->class = EstimatesGoodsItem::class;
        $this->model = 'App\EstimatesGoodsItem';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Reservable;

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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $success = true;
        $stockId = null;

        // Если включены настройки для складов, то проверяем сколько складов в системе, и если один, то берем его id
        $settings = getSettings();
        if ($settings->isNotEmpty()) {
            $stocks = Stock::where('filial_id', auth()->user()->stafferFilialId)
                ->get([
                    'id',
                    'filial_id'
                ]);

            if ($stocks) {
                if ($stocks->count() == 1) {
                    $stockId = $stocks->first()->id;
                }
            }
        }

        $priceGoods = PricesGoods::with([
            'product.article'
        ])
            ->find($request->price_id);

        if ($priceGoods->product->serial == 1) {

            $data = [
                'estimate_id' => $request->estimate_id,
                'goods_id' => $priceGoods->product->id,
                'price_id' => $priceGoods->id,
                'currency_id' => $priceGoods->currency_id,
                'stock_id' => $stockId,
                'price' => $priceGoods->price,
//                'discount_percent' => $discountPercent,
//                'discount_currency' => $discountCurrency,
                'count' => 1,
                'sale_mode' => 1,
                'cost' => $priceGoods->product->article->cost_default,
                'amount' => $priceGoods->price,
                'margin_currency' => ($priceGoods->price - $priceGoods->discount_currency) - $priceGoods->product->article->cost_default,
                'total' => $priceGoods->price - $priceGoods->discount_currency,
            ];

            // $onePercent = $data['amount'] / 100;
            $data['margin_percent'] = ($data['margin_currency'] / $data['total'] * 100);

            $estimatesGoodsItem = EstimatesGoodsItem::create($data);

        } else {
            $estimatesGoodsItem = EstimatesGoodsItem::firstOrNew([
                'estimate_id' => $request->estimate_id,
                'price_id' => $priceGoods->id,
            ], [
                'goods_id' => $priceGoods->product->id,
                'currency_id' => $priceGoods->currency_id,
                'stock_id' => $stockId,
                'sale_mode' => 1,

                'cost_unit' => $priceGoods->product->article->cost_default,
                'price' => $priceGoods->price,
                'points' => $priceGoods->points,
                'count' => 1,

                'price_discount_id' => $priceGoods->price_discount_id,
                'price_discount_unit' => $priceGoods->price_discount,

                'catalogs_item_discount_id' => $priceGoods->catalogs_item_discount_id,
                'catalogs_item_discount_unit' => $priceGoods->catalogs_item_discount,

                'estimate_discount_id' => $priceGoods->estimate_discount_id,
                'estimate_discount_unit' => $priceGoods->estimate_discount,

                'client_discount_percent' => $request->client_discount_percent,

                'manual_discount_currency' => 0,
            ]);


            if ($estimatesGoodsItem->exists) {

                if ($estimatesGoodsItem->price != $priceGoods->price) {
                    $success = false;
                } else {
                    $data = $request->input();
                    $data['count'] = $estimatesGoodsItem->count + 1;
                    $estimatesGoodsItem->update($data);
                }
            } else {
                $estimatesGoodsItem->save();
            }
        }

        if ($success) {
            $this->estimateUpdate($estimatesGoodsItem->estimate);

            $result = [
                'success' => $success,
                'item' => $estimatesGoodsItem
            ];
        } else {
            $result = [
                'success' => $success,
            ];
        }

        $estimatesGoodsItem->load([
            'product.article',
            'price_goods',
            'reserve',
            'stock:id,name',
            'currency'
        ]);

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\EstimatesGoodsItem $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\EstimatesGoodsItem $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\EstimatesGoodsItem $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $estimatesGoodsItem = EstimatesGoodsItem::with([
            'product.article',
        ])
            ->find($id);
//        dd($estimatesGoodsItem);

        $merge = 0;

        $data = $request->input();


        // Обновляем комментарий
        if ($request->has('comment')) {
            $data['comment'] = $request->comment;
        }

        // Меняем режим продажи
        if ($request->has('sale_mode')) {
            $saleMode = $request->sale_mode;
            $data['sale_mode'] = $saleMode;

            // Проверяем на наличие в смете такого же товара с таким же режимом продажи
            $oldEstimatesGoodsItem = EstimatesGoodsItem::where([
                'estimate_id' => $estimatesGoodsItem->estimate_id,
                'goods_id' => $estimatesGoodsItem->goods_id,
                'price_id' => $estimatesGoodsItem->price_id,
//                'stock_id' => $estimatesGoodsItem->stock_id,
                'sale_mode' => $saleMode,
            ])
                ->where('id', '!=', $estimatesGoodsItem->id)
                ->first();
//            dd($oldEstimatesGoodsItem);

            // Если существует позиция с режимом, на который мы поменяли текущую позицию (т.е. ее аналог)
            if ($oldEstimatesGoodsItem) {
                $merge = $estimatesGoodsItem->id;
                $data['count'] = $oldEstimatesGoodsItem->count + $estimatesGoodsItem->count;
                $estimatesGoodsItem = $oldEstimatesGoodsItem;
            }

        }

        $result = $estimatesGoodsItem->update($data);
//        dd($result);

        $estimatesGoodsItem->load([
            'product.article',
            'price_goods',
            'reserve',
            'stock:id,name',
            'currency'
        ]);
        $this->estimateUpdate($estimatesGoodsItem->estimate);

        if ($merge > 0) {
            $estimatesGoodsItem->remove_from_page = $merge;
        }
//	    dd($estimatesGoodsItem);

        return response()->json($estimatesGoodsItem);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $estimatesGoodsItem = EstimatesGoodsItem::with([
            'estimate',
            'product',
            'document',
            'reserve'
        ])
            ->find($id);

        if (isset($estimatesGoodsItem->reserve)) {
            Log::channel('documents')
                ->info('========================================== УДАЛЯЕМ ПУНКТ СМЕТЫ, ИМЕЮЩИЙ РЕЗЕРВ, ID: ' . $estimatesGoodsItem->id . ' ==============================================');
            $this->unreserve($estimatesGoodsItem);
            $result = $estimatesGoodsItem->delete();
            Log::channel('documents')
                ->info('========================================== КОНЕЦ УДАЛЕНИЯ ПУНКТА СМЕТЫ, ИМЕЮЩЕГО РЕЗЕРВ ==============================================

                ');
        } else {
            $result = $estimatesGoodsItem->forceDelete();
        }

        $this->estimateUpdate($estimatesGoodsItem->estimate);
        return response()->json($result);
    }

    /**
     * Обновление итоговых значений сметы
     *
     * @param $estimate
     */
    public function estimateUpdate($estimate)
    {
        $estimate->load([
            'goods_items',
            'services_items',
        ]);

        $cost = 0;
        $amount = 0;
        $points = 0;

        $priceDiscount = 0;
        $catalogsItemDiscount = 0;
        $estimateDiscount = 0;
        $clientDiscount = 0;
        $manualDiscount = 0;

        $total = 0;
        $totalPoints = 0;
        $totalBonuses = 0;

        if ($estimate->goods_items->isNotEmpty()) {
            $cost += $estimate->goods_items->sum('cost');
            $amount += $estimate->goods_items->sum('amount');
            $points += $estimate->goods_items->sum('points');

            $priceDiscount += $estimate->goods_items->sum('price_discount');
            $catalogsItemDiscount += $estimate->goods_items->sum('catalogs_item_discount');
            $estimateDiscount += $estimate->goods_items->sum('estimate_discount');
            $clientDiscount += $estimate->goods_items->sum('client_discount_currency');
            $manualDiscount += $estimate->goods_items->sum('manual_discount_currency');

            $total += $estimate->goods_items->sum('total');
            $totalPoints += $estimate->goods_items->sum('total_points');
            $totalBonuses += $estimate->goods_items->sum('total_bonuses');
        }

//        if ($estimate->services_items->isNotEmpty()) {
//            $cost += $estimate->services_items->sum('cost');
//            $amount += $estimate->services_items->sum('amount');
//            $total += $estimate->services_items->sum('total');
//        }


        // Скидки
        $discountCurrency = 0;
        $discountPercent = 0;
        if ($total > 0) {
            $discountCurrency = $amount - $total;
            $discountPercent = $discountCurrency * 100 / $amount;
        }

        // Маржа
        $marginCurrency = $total - $cost;
        if ($total > 0) {
            $marginPercent = ($marginCurrency / $total * 100);
        } else {
            $marginPercent = $marginCurrency * 100;
        }

        $data = [
            'cost' => $cost,
            'amount' => $amount,
            'points' => $points,

            'price_discount' => $priceDiscount,
            'catalogs_item_discount' => $catalogsItemDiscount,
            'estimate_discount' => $estimateDiscount,
            'client_discount' => $clientDiscount,
            'manual_discount' => $manualDiscount,

            'discount_currency' => $discountCurrency,
            'discount_percent' => $discountPercent,

            'total' => $total,
            'total_points' => $totalPoints,
            'total_bonuses' => $totalBonuses,

            'margin_currency' => $marginCurrency,
            'margin_percent' => $marginPercent,
        ];

        $estimate->update($data);
    }

    public function reserving(Request $request, $id)
    {

        $estimates_goods_item = EstimatesGoodsItem::with([
            'product.article',
            'document',
            'reserve'
        ])
            ->find($id);

        Log::channel('documents')
            ->info('========================================== НАЧАЛО РЕЗЕРВИРОВАНИЯ ПУНКТА СМЕТЫ, ID: ' . $estimates_goods_item->id . ' ==============================================');

        $result = $this->reserve($estimates_goods_item);

        Log::channel('documents')
            ->info('========================================== КОНЕЦ РЕЗЕРВИРОВАНИЯ ПУНКТА СМЕТЫ ==============================================
                
                ');

        $estimates_goods_item->load([
            'product.article',
            'reserve',
            'stock:id,name'
        ]);

        return response()->json([
            'item' => $estimates_goods_item,
            'msg' => $result
        ]);
    }

    public function unreserving(Request $request, $id)
    {

        $estimates_goods_item = EstimatesGoodsItem::with([
            'product.article',
            'document',
            'reserve'
        ])
            ->find($id);

        Log::channel('documents')
            ->info('========================================== НАЧАЛО СНЯТИЯ РЕЗЕРВИРОВАНИЯ ПУНКТА СМЕТЫ, ID: ' . $estimates_goods_item->id . ' ==============================================');

        $result = $this->unreserve($estimates_goods_item);

        Log::channel('documents')
            ->info('========================================== КОНЕЦ СНЯТИЯ РЕЗЕРВИРОВАНИЯ ПУНКТА СМЕТЫ ==============================================
                
                ');

        $estimates_goods_item->load([
            'product.article',
            'reserve',
            'stock:id,name'
        ]);

        return response()->json([
            'item' => $estimates_goods_item,
            'msg' => $result
        ]);
    }
}
