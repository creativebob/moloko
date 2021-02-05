<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Http\Controllers\System\Traits\Cancelable;
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

    use Cancelable;

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductionsItemStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductionsItemStoreRequest $request)
    {
        $data = $request->input();

        $entity = Entity::find($request->entity_id);
        $data['cmv_type'] = $entity->model;

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

    /**
     * Отмена производства пункта
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function cancel($id)
    {
        $productionsItem = ProductionsItem::with([
            'cmv' => function ($q) {
                $q->with([
                    'article',
                    'cost'
                ]);
            },
            'entity',
            'receipt.storage',
            'offs' => function ($q) {
                $q->with([
                    'cmv' => function ($q) {
                        $q->with([
                            'cost',
                            'stocks',
                            'article'
                        ]);
                    },
                    'storage'
                ]);
            },
            'document'
        ])
            ->find($id);
//        dd($productionsItem);

        if (empty($productionsItem)) {
            abort(403, __('errors.not_found'));
        }

        $receipt = $productionsItem->receipt;
        $storage = $receipt->storage;

        if ($storage->free < $receipt->count) {
            return back()
                ->withErrors(['msg' => 'В пункте на остатках нет нужного количества для возврата!']);
        }

        logs('documents')
            ->info('========================================== ОТМЕНА ПОЗИЦИИ НАРЯДА ПРОИЗВОДСТВА ==============================================');

        $this->cancelOffs($productionsItem);

        $this->cancelReceipt($productionsItem);

        $res = $productionsItem->delete();

        logs('documents')
            ->info("Удалена позиция: {$productionsItem->id}");
        logs('documents')
            ->info('========================================== КОНЕЦ ОТМЕНЫ ПОЗИЦИИ НАРЯДА ПРОИЗВОДСТВА ==============================================

				');
        if ($res) {
            return redirect()->route('productions.edit', $productionsItem->document->id);
        } else {
            abort(403, __('errors.destroy'));
        }

    }
}
