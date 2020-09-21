<?php

namespace App\Http\Controllers;

use App\ConsignmentsItem;
use App\Http\Requests\System\ConsignmentsItemStoreRequest;
use App\Http\Requests\System\ConsignmentsItemUpdateRequest;
use Illuminate\Http\Request;

class ConsignmentsItemController extends Controller
{

    // Настройки сконтроллера
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(ConsignmentsItemStoreRequest $request)
    {
        $data = $request->input();
        $consignment_item = ConsignmentsItem::create($data);

        $consignment_item->load([
            'cmv.article.unit',
            'entity:id,name,alias',
            'manufacturer.company',
            'currency'
        ]);

        return response()->json($consignment_item);
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        //
    }

    public function update(ConsignmentsItemUpdateRequest $request, $id)
    {
        $consignment_item = ConsignmentsItem::find($id);

        $data = $request->input();
        $consignment_item->update($data);

        $consignment_item->load([
            'cmv.article.unit',
            'entity:id,name,alias',
            'currency'
        ]);

        return response()->json($consignment_item);
    }

    public function destroy($id)
    {
        $result = ConsignmentsItem::destroy($id);
        return response()->json($result);
    }
}
