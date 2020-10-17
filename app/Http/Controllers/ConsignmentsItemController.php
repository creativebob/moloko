<?php

namespace App\Http\Controllers;

use App\Models\System\Documents\ConsignmentsItem;
use App\Http\Requests\System\ConsignmentsItemStoreRequest;
use App\Http\Requests\System\ConsignmentsItemUpdateRequest;

class ConsignmentsItemController extends Controller
{

    /**
     * ConsignmentsItemController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ConsignmentsItemStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ConsignmentsItemStoreRequest $request)
    {
        $data = $request->input();
        $consignmentsItem = ConsignmentsItem::create($data);

        $consignmentsItem->load([
            'cmv.article.unit',
            'entity:id,name,alias',
            'manufacturer.company',
            'currency'
        ]);

        return response()->json($consignmentsItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ConsignmentsItemUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ConsignmentsItemUpdateRequest $request, $id)
    {
        $consignmentsItem = ConsignmentsItem::find($id);

        $data = $request->input();
        $consignmentsItem->update($data);

        $consignmentsItem->load([
            'cmv.article.unit',
            'entity:id,name,alias',
            'currency'
        ]);

        return response()->json($consignmentsItem);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = ConsignmentsItem::destroy($id);
        return response()->json($result);
    }
}
