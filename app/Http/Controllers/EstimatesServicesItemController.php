<?php

namespace App\Http\Controllers;

use App\EstimatesServicesItem;
use App\PricesService;
use Illuminate\Http\Request;

class EstimatesServicesItemController extends Controller
{

    /**
     * EstimatesServicesItemController constructor.
     * @param EstimatesServicesItem $estimates_services_item
     */
    public function __construct(EstimatesServicesItem $estimates_services_item)
    {
        $this->middleware('auth');
        $this->estimate = $estimates_services_item;
        $this->class = EstimatesServicesItem::class;
        $this->model = 'App\EstimatesServicesItem';
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        $success = true;

        $prices_service = PricesService::findOrFail($request->price_id);
        $prices_service->load('product');

        if ($prices_service->product->serial == 1) {
            $estimates_services_item = EstimatesServicesItem::create([
                'estimate_id' => $request->estimate_id,
                'service_id' => $prices_service->product->id,
                'price_id' => $prices_service->id,
                'currency_id' => $prices_service->currency_id,
                'price' => $prices_service->price,
                'count' => 1,
                'amount' => $prices_service->price
            ]);
        }  else {

            // TODO - 28.10.19 - Проверка при добавлении при множественном клике в смете, уйдет с Vue

            $estimates_services_item = EstimatesServicesItem::firstOrNew([
                'estimate_id' => $request->estimate_id,
                'service_id' => $prices_service->product->id,
                'price_id' => $prices_service->id,
            ], [
                'price' => $prices_service->price,
                'count' => 1,
                'amount' => $prices_service->price,
                'currency_id' => $prices_service->currency_id,
            ]);

            if ($estimates_services_item->id) {

                if ($estimates_services_item->price != $prices_service->price) {
                    $success = false;
                } else {
                    $count = $estimates_services_item->count + 1;
                    $amount = $count * $estimates_services_item->price;

                    $estimates_services_item->update([
                        'count' => $count,
                        'amount' => $amount
                    ]);
                }
            } else {
                $estimates_services_item->save();
            }
        }

        if ($success) {
            $estimates_services_item->load([
                'product.process',
            ]);
            $this->estimateUpdate($estimates_services_item->estimate);

            $result = [
                'success' => $success,
                'item' => $estimates_services_item
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
        $estimates_services_item = EstimatesServicesItem::findOrFail($id);
        // dd($estimates_goods_item);

        $result = $estimates_services_item->update([
            'count' => $request->count,
        ]);
//        dd($result);

        $estimates_services_item->load([
            'product.process',
        ]);
        $this->estimateUpdate($estimates_services_item->estimate);

        return response()->json($estimates_services_item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EstimatesGoodsItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $estimates_services_item = EstimatesServicesItem::with([
            'product',
            'document',
        ])
            ->findOrFail($id);

        $result = $estimates_services_item->forceDelete();
        $this->estimateUpdate($estimates_services_item->estimate);

        return response()->json($result);
    }

    public function estimateUpdate($estimate)
    {
        $estimate->load([
            'goods_items',
            'services_items'
        ]);

        $amount = 0;
        if ($estimate->services_items->isNotEmpty()) {
            $amount += $estimate->services_items->sum('amount');
        }
        if ($estimate->goods_items->isNotEmpty()) {
            $amount += $estimate->goods_items->sum('amount');
        }

        if ($amount > 0) {
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
    }
}
