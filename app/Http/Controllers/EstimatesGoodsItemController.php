<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Estimatable;
use App\Models\System\Documents\Estimate;
use App\Models\System\Documents\EstimatesGoodsItem;
use App\Stock;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\Reservable;
use App\PricesGoods;
use Illuminate\Http\Request;

class EstimatesGoodsItemController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * EstimatesGoodsItemController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'estimates_goods_items';
        $this->entityDependence = false;
    }

    use Estimatable,
        Reservable;

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $success = true;

        // TODO - 16.10.20 - Возня со складом (Пока что берем первый склад) - видимо устарело с внедрением торговых точек
//        $stockId = null;
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
//        $answer = operator_right('stocks', true, getmethod('index'));
//        $stockId = Stock::
//        where('filial_id', auth()->user()->stafferFilialId)
//            ->moderatorLimit($answer)
//            ->companiesLimit($answer)
            // ->filials($answer)
//            ->authors($answer)
//            ->systemItem($answer)
//            ->
//            value('id');

        // Если включены настройки для складов, то проверяем сколько складов в системе, и если один, то берем его id
//        $settings = getSettings();
//        if ($settings->isNotEmpty()) {
//            $stocks = Stock::moderatorLimit($answer)
//                ->companiesLimit($answer)
//                // ->filials($answer)
//                ->authors($answer)
//                ->systemItem($answer)
//                ->get();
////            $stocks = Stock::where('filial_id', auth()->user()->stafferFilialId)
////                ->get([
////                    'id',
////                    'filial_id'
////                ]);
//
//            if ($stocks) {
//                if ($stocks->count() == 1) {
//                    $stockId = $stocks->first()->id;
//                } else {
//                    $stockId = $stocks->first()->id;
//                }
//            }
//        } else {
//            $stockId = Stock::moderatorLimit($answer)
//                ->companiesLimit($answer)
//                // ->filials($answer)
//                ->authors($answer)
//                ->systemItem($answer)
//                ->value('id');
//        }

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
                'stock_id' => $request->stock_id,
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
                'stock_id' => $request->stock_id,
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
            $this->aggregateEstimate($estimatesGoodsItem->estimate);

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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\System\Documents\EstimatesGoodsItem $estimatesItem
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
        $this->aggregateEstimate($estimatesGoodsItem->estimate);

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
            logs('documents')
                ->info('========================================== УДАЛЯЕМ ПУНКТ СМЕТЫ, ИМЕЮЩИЙ РЕЗЕРВ, ID: ' . $estimatesGoodsItem->id . ' ==============================================');
            $this->cancelReserve($estimatesGoodsItem);
            $result = $estimatesGoodsItem->delete();
            logs('documents')
                ->info('========================================== КОНЕЦ УДАЛЕНИЯ ПУНКТА СМЕТЫ, ИМЕЮЩЕГО РЕЗЕРВ ==============================================

                ');
        } else {
            $result = $estimatesGoodsItem->forceDelete();
        }

        $this->aggregateEstimate($estimatesGoodsItem->estimate);
        return response()->json($result);
    }

    /**
     * Резерв пункта сметы
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reserving($id)
    {
        $estimatesGoodsItem = EstimatesGoodsItem::with([
            'product.article',
            'document',
            'reserve'
        ])
            ->find($id);

        logs('documents')
            ->info('========================================== НАЧАЛО РЕЗЕРВИРОВАНИЯ ПУНКТА СМЕТЫ, ID: ' . $estimatesGoodsItem->id . ' ==============================================');
        // Еслои количество пришедшее количество не равно количеству в бд
//        if ($estimatesGoodsItem->count != $request->count) {
//            $estimatesGoodsItem->update([
//                'count' => $request->count
//            ]);
//            $estimatesGoodsItem->count = $request->count;
//            $this->aggregateEstimate($estimatesGoodsItem->estimate);
//        }

        $result = $this->reserve($estimatesGoodsItem);

        logs('documents')
            ->info('========================================== КОНЕЦ РЕЗЕРВИРОВАНИЯ ПУНКТА СМЕТЫ ==============================================

                ');

        $estimatesGoodsItem->load([
            'stock:id,name',
            'price_goods',
            'goods.article',
            'currency',
            'reserve'
        ]);

        return response()->json([
            'item' => $estimatesGoodsItem,
            'msg' => $result
        ]);
    }

    /**
     * Отмена резерва пункта сметы
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreserving($id)
    {

        $estimatesGoodsItem = EstimatesGoodsItem::with([
            'product.article',
            'document',
            'reserve'
        ])
            ->find($id);

        logs('documents')
            ->info('========================================== НАЧАЛО СНЯТИЯ РЕЗЕРВИРОВАНИЯ ПУНКТА СМЕТЫ, ID: ' . $estimatesGoodsItem->id . ' ==============================================');

        $result = $this->cancelReserve($estimatesGoodsItem);

        logs('documents')
            ->info('========================================== КОНЕЦ СНЯТИЯ РЕЗЕРВИРОВАНИЯ ПУНКТА СМЕТЫ ==============================================

                ');

        $estimatesGoodsItem->load([
            'stock:id,name',
            'price_goods',
        ]);

        return response()->json([
            'item' => $estimatesGoodsItem,
            'msg' => $result
        ]);
    }
}
