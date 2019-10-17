<?php

namespace App\Http\Controllers;

use App\EstimatesServicesItem;
use App\PricesService;
use Illuminate\Http\Request;

class EstimatesServicesItemController extends Controller
{

    // Настройки сконтроллера
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $prices_service = PricesService::findOrFail($request->price_id);
        $prices_service->load('product');

        $estimates_services_item = EstimatesServicesItem::create([
            'estimate_id' => $request->estimate_id,
            'service_id' => $prices_service->product->id,
            'price_id' => $prices_service->id,
            'price' => $prices_service->price,
            'count' => 1,
            'amount' => $prices_service->price
        ]);

        $estimates_services_item->load('product.process');
        return view('leads.estimate.estimates_services_item', compact('estimates_services_item'));
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
        $estimates_services_item = EstimatesServicesItem::findOrFail($id);

        $count = $estimates_services_item->count + 1;
        $amount = $count * $estimates_services_item->price;

        $estimates_services_item->update([
            'count' => $count,
            'amount' => $amount
        ]);

        // dd($estimates_goods_item);

        $estimates_services_item->load('product.process');
        return view('leads.estimate.estimates_services_item', compact('estimates_services_item'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EstimatesGoodsItem  $estimatesItem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = EstimatesServicesItem::destroy($id);
        return response()->json($result);
    }
}
