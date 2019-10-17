<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductionsItemStoreRequest;
use App\Http\Requests\ProductionsItemUpdateRequest;
use App\ProductionsItem;
use Illuminate\Http\Request;

class ProductionsItemController extends Controller
{

    // Настройки сконтроллера
    public function __construct()
    {
        $this->middleware('auth');
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
    public function store(ProductionsItemStoreRequest $request)
    {
        $data = $request->input();
        $production_item = (new ProductionsItem())->create($data);

        $production_item->load([
            'cmv.article.unit',
            'entity:id,name,alias'
        ]);

        return response()->json($production_item);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProductionsItem  $productionsItem
     * @return \Illuminate\Http\Response
     */
    public function show(ProductionsItem $productionsItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductionsItem  $productionsItem
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductionsItem $productionsItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductionsItem  $productionsItem
     * @return \Illuminate\Http\Response
     */
    public function update(ProductionsItemUpdateRequest $request, $id)
    {
        $production_item = ProductionsItem::findOrFail($id);

        $data = $request->input();
        $production_item->update($data);

        $production_item->load([
            'cmv.article.unit',
            'entity:id,name,alias'
        ]);

        return response()->json($production_item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductionsItem  $productionsItem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = ProductionsItem::destroy($id);
        return response()->json($result);
    }
}
