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
     * @param EstimatesGoodsItem $estimates_goods_item
     */
    public function __construct(EstimatesGoodsItem $estimates_goods_item)
    {
        $this->middleware('auth');
        $this->estimate = $estimates_goods_item;
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

        $price_goods = PricesGoods::with([
            'product.article'
        ])
        ->findOrFail($request->price_id);

        if ($price_goods->product->serial == 1) {

            $data = [
                'estimate_id' => $request->estimate_id,
                'goods_id' => $price_goods->product->id,
                'price_id' => $price_goods->id,
                'currency_id' => $price_goods->currency_id,
                'stock_id' => $stock_id,
                'price' => $price_goods->price,
                'count' => 1,
                'cost' => $price_goods->product->article->cost_default,
                'amount' => $price_goods->price,
                'margin_currency' => $price_goods->price - $price_goods->product->article->cost_default,
            ];

            $onePercent = $data['amount'] / 100;
            $data['margin_percent'] = ($data['cost'] / $onePercent);

            $estimates_goods_item = EstimatesGoodsItem::create($data);

        } else {
            $estimates_goods_item = EstimatesGoodsItem::firstOrNew([
                'estimate_id' => $request->estimate_id,
                'goods_id' => $price_goods->product->id,
                'price_id' => $price_goods->id,
                'stock_id' => $stock_id,
            ], [
                'price' => $price_goods->price,
                'count' => 1,
                'cost' => $price_goods->product->article->cost_default,
                'amount' => $price_goods->price,
                'margin_currency' => $price_goods->price - $price_goods->product->article->cost_default,
                'currency_id' => $price_goods->currency_id,
            ]);

            $onePercent = $estimates_goods_item->amount / 100;
            $estimates_goods_item->margin_percent = ($estimates_goods_item->cost / $onePercent);

            if ($estimates_goods_item->id) {

                if ($estimates_goods_item->price != $price_goods->price) {
                    $success = false;
                } else {

                    $data['count'] = $estimates_goods_item->count + 1;
                    $data['amount'] = $data['count'] * $estimates_goods_item->price;
                    $data['cost'] = $data['count'] * $price_goods->product->article->cost_default;

                    $data['margin_currency'] = $data['amount'] - $data['cost'];

                    $onePercent = $data['amount'] / 100;
                    $data['margin_percent'] = ($data['cost'] / $onePercent);

                    $estimates_goods_item->update($data);
                }
            } else {
                $estimates_goods_item->save();
            }
        }

        if ($success) {
            $estimates_goods_item->load([
                'product.article',
                'reserve',
                'stock:id,name'
            ]);
            $this->estimateUpdate($estimates_goods_item->estimate);

            $result = [
                'success' => $success,
                'item' => $estimates_goods_item
            ];
        } else {
            $result = [
                'success' => $success,
            ];
        }

        return response()->json($result);
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
        $estimates_goods_item = EstimatesGoodsItem::with([
            'product.article'
        ])
        ->findOrFail($id);
        // dd($estimates_goods_item);

        $data['count'] = $request->count;
        $data['amount'] = $estimates_goods_item->price *  $data['count'];
        $data['cost'] = $data['count'] * $estimates_goods_item->product->article->cost_default;

        $data['margin_currency'] = $data['amount'] - $data['cost'];

        $onePercent = $data['amount'] / 100;
        $data['margin_percent'] = ($data['cost'] / $onePercent);

        $result = $estimates_goods_item->update($data);
//        dd($result);

        $estimates_goods_item->load([
            'product.article',
            'reserve',
            'stock:id,name'
        ]);
	    $this->estimateUpdate($estimates_goods_item->estimate);

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
	    $estimates_goods_item = EstimatesGoodsItem::with([
            'product',
            'document',
            'reserve'
        ])
        ->findOrFail($id);

	    if (isset($estimates_goods_item->reserve)) {
            Log::channel('documents')
                ->info('========================================== УДАЛЯЕМ ПУНКТ СМЕТЫ, ИМЕЮЩИЙ РЕЗЕРВ, ID: ' . $estimates_goods_item->id . ' ==============================================');
            $this->unreserve($estimates_goods_item);
            $result = $estimates_goods_item->delete();
            Log::channel('documents')
                ->info('========================================== КОНЕЦ УДАЛЕНИЯ ПУНКТА СМЕТЫ, ИМЕЮЩЕГО РЕЗЕРВ ==============================================
                
                ');
        } else {
            $result = $estimates_goods_item->forceDelete();
        }

	    $this->estimateUpdate($estimates_goods_item->estimate);
//        $result = EstimatesGoodsItem::destroy($id);
        return response()->json($result);
    }

    public function estimateUpdate($estimate)
    {
        $estimate->load([
            'goods_items',
            'services_items'
        ]);

        $amount = 0;
        $cost = 0;
        if ($estimate->services_items->isNotEmpty()) {
            $amount += $estimate->services_items->sum('amount');
            $cost += $estimate->services_items->sum('cost');
        }
        if ($estimate->goods_items->isNotEmpty()) {
            $amount += $estimate->goods_items->sum('amount');
            $cost += $estimate->goods_items->sum('cost');
        }

        if ($amount > 0) {
            $discount = (($amount * $estimate->discount_percent) / 100);
            $total = ($amount - $discount);

            $margin_currency = $total - $cost;

            $onePercent = $total / 100;
            $margin_percent = ($cost / $onePercent);

            $data = [
                'amount' => $amount,
                'discount' => $discount,
                'total' => $total,
                'cost' => $cost,
                'margin_currency' => $margin_currency,
                'margin_percent' => $margin_percent,
            ];

        } else {
            $data = [
                'amount' => 0,
                'discount' => 0,
                'total' => 0,
                'cost' => 0,
                'margin_currency' => 0,
                'margin_percent' => 0,
            ];
        }

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
