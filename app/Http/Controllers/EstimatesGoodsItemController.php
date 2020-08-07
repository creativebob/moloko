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
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $success = true;
        $stock_id = null;

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
                    $stock_id = $stocks->first()->id;
                }
            }
        }

        $priceGoods = PricesGoods::with([
            'product.article'
        ])
        ->findOrFail($request->price_id);

        if ($priceGoods->product->serial == 1) {

            $data = [
                'estimate_id' => $request->estimate_id,
                'goods_id' => $priceGoods->product->id,
                'price_id' => $priceGoods->id,
                'currency_id' => $priceGoods->currency_id,
                'stock_id' => $stock_id,
                'price' => $priceGoods->price,
                'count' => 1,
                'sale_mode' => 1,
                'cost' => $priceGoods->product->article->cost_default,
                'amount' => $priceGoods->price,
                'margin_currency' => $priceGoods->price - $priceGoods->product->article->cost_default,
                'total' => $priceGoods->price,
            ];

            // $onePercent = $data['amount'] / 100;
            $data['margin_percent'] = ($data['margin_currency'] / $data['total'] * 100);

            $estimatesGoodsItem = EstimatesGoodsItem::create($data);

        } else {
            $estimatesGoodsItem = EstimatesGoodsItem::firstOrNew([
                'estimate_id' => $request->estimate_id,
                'goods_id' => $priceGoods->product->id,
                'price_id' => $priceGoods->id,
                'stock_id' => $stock_id,
                'sale_mode' => 1,
            ], [
                'price' => $priceGoods->price,
                'count' => 1,
                'cost' => $priceGoods->product->article->cost_default,
                'points' => $priceGoods->points,
                'currency_id' => $priceGoods->currency_id,
                'amount' => $priceGoods->price,
                'margin_currency' => $priceGoods->price - $priceGoods->product->article->cost_default,
                'total' => $priceGoods->price,
            ]);

//            $onePercent = $estimatesGoodsItem->amount / 100;
            $estimatesGoodsItem->margin_percent = ($estimatesGoodsItem->margin_currency / $estimatesGoodsItem->amount * 100);

            if ($estimatesGoodsItem->id) {

                if ($estimatesGoodsItem->price != $priceGoods->price) {
                    $success = false;
                } else {

                    $data['count'] = $estimatesGoodsItem->count + 1;
                    $data['cost'] = $data['count'] * $priceGoods->product->article->cost_default;
                    $data['amount'] = $data['count'] * $estimatesGoodsItem->price;
                    $data['total'] = $data['count'] * $estimatesGoodsItem->price;

                    $data['margin_currency'] = $data['total'] - $data['cost'];
                    $data['margin_percent'] = ($data['margin_currency'] / $data['total'] * 100);

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
     * Отображение указанного ресурса.
     *
     * @param  \App\EstimatesGoodsItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\EstimatesGoodsItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\EstimatesGoodsItem $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $estimatesGoodsItem = EstimatesGoodsItem::with([
            'product.article'
        ])
        ->findOrFail($id);
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
     * Удаление указанного ресурса из хранилища.
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
        ->findOrFail($id);

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
            'services_items'
        ]);

        $cost = 0;
        $amount = 0;
        $total = 0;
        $points = 0;
        $discountItemsCurrency = 0;
        $totalPoints = 0;
        $totalBonuses = 0;

        if ($estimate->services_items->isNotEmpty()) {
            $cost += $estimate->services_items->sum('cost');
            $amount += $estimate->services_items->sum('amount');
            $total += $estimate->services_items->sum('total');
        }
        if ($estimate->goods_items->isNotEmpty()) {
            $cost += $estimate->goods_items->sum('cost');
            $amount += $estimate->goods_items->sum('amount');
            $total += $estimate->goods_items->sum('total');
            $points += $estimate->goods_items->sum('points');
            $discountItemsCurrency += $estimate->goods_items->sum('discount_currency');
            $totalPoints += $estimate->goods_items->sum('total_points');
            $totalBonuses += $estimate->goods_items->sum('total_bonuses');
        }

        $marginCurrency = 0;
        $marginPercent = 0;
        $discount = 0;

        if ($amount > 0) {
            $discount = (($amount * $estimate->discount_percent) / 100);
            $marginCurrency = $total - $cost;
            $marginPercent = ($marginCurrency / $total * 100);
        }

        $data = [
            'cost' => $cost,
            'amount' => $amount,
            'discount' => $discount,
            'total' => $total,
            'margin_currency' => $marginCurrency,
            'margin_percent' => $marginPercent,
            'points' => $points,
            'discount_items_currency' => $discountItemsCurrency,
            'total_points' => $totalPoints,
            'total_bonuses' => $totalBonuses,
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
        ->findOrfail($id);

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
            ->findOrfail($id);

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
