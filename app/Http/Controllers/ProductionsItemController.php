<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\ProductionsItemStoreRequest;
use App\Http\Requests\System\ProductionsItemUpdateRequest;
use App\Models\System\Documents\ProductionsItem;

class ProductionsItemController extends Controller
{

    /**
     * ProductionsItemController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductionsItemStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductionsItemStoreRequest $request)
    {
        $data = $request->input();
        $productionsItem = ProductionsItem::create($data);

        $productionsItem->load([
            'cmv.article.unit',
            'entity:id,name,alias'
        ]);

        return response()->json($productionsItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductionsItemUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductionsItemUpdateRequest $request, $id)
    {
        $productionsItem = ProductionsItem::find($id);

        $data = $request->input();
        $productionsItem->update($data);

        $productionsItem->load([
            'cmv.article.unit',
            'entity:id,name,alias'
        ]);

        return response()->json($productionsItem);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = ProductionsItem::destroy($id);
        return response()->json($result);
    }
}
