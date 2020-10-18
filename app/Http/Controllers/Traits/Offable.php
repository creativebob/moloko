<?php

namespace App\Http\Controllers\Traits;

use App\Entity;
use App\Off;
use App\Stock;

trait Offable
{

    /**
     * Списывание состава со склада для производства
     *
     * @param $item
     * @return array
     */
    public function production($item)
    {
        $cost = 0;
        $isWrong = 0;

        $relationsNames = [
            'raws',
            'containers',
            'attachments'
        ];

        $documentModel = Entity::where('alias', $item->document->getTable())
            ->value('model');

        if ($item->document->getTable() == 'estimates') {
            $modelDocumentItem = $documentModel . 'sGoodsItem';
        } else {
            $modelDocumentItem = $documentModel . 'sItem';
        }

        foreach ($relationsNames as $relationName) {
            if ($item->cmv->article->$relationName->isNotEmpty()) {

                $compositionModel = Entity::where('alias', $relationName)
                    ->value('model');

                foreach ($item->cmv->article->$relationName as $composition) {

                    logs('documents')
                        ->info('=== СПИСАНИЕ ' . $composition->getTable() . ' ' . $composition->id . ' ===');

                    // Списываем позицию состава
                    $stockGeneral = Stock::find($item->document->stock_id);
//                    dd($stock_production);

                    $composition->load([
                        'stocks'
                    ]);
                    $stockComposition = $composition->stocks->where('stock_id', $stockGeneral->id)
                        ->where('filial_id', $stockGeneral->filial_id)
                        ->where('manufacturer_id', $composition->article->manufacturer_id)
                        ->first();
                    if ($stockComposition) {
                        logs('documents')
                            ->info('Существует склад ' . $stockComposition->getTable() . ' c id: ' . $stockComposition->id);
                    } else {
                        $dataStock = [
                            'cmv_id' => $composition->id,
                            'manufacturer_id' => $composition->article->manufacturer_id,
                            'stock_id' => $item->document->stock_id,
                            'filial_id' => $item->document->filial_id,
                        ];
                        $compositionStockModel = Entity::where('alias', $relationName . '_stocks')
                            ->value('model');

                        $stockComposition = $compositionStockModel::create($dataStock);

                        logs('documents')
                            ->info('Создан склад ' . $stockComposition->getTable() . ' c id: ' . $stockComposition->id);

                        $isWrong = 1;
                    }

                    logs('documents')
                        ->info('Значения count: ' . $stockComposition->count . ', weight: ' . $stockComposition->weight . ', volume: ' . $stockComposition->volume);

                    // Получаем себестоимость
                    $count = $composition->pivot->value;

                    $newCount = $stockComposition->count -= ($composition->portion * $count * $item->count);

                    $data = [
                        'count' => $newCount,
                        'free' => $newCount > 0 ? $newCount : 0,
                        'weight' => $stockComposition->weight -= ($composition->weight * $count * $item->count),
                        'volume' => $stockComposition->volume -= ($composition->volume * $count * $item->count)
                    ];
                    $stockComposition->update($data);

                    logs('documents')
                        ->info('Обновлены значения count: ' . $stockComposition->count . ', weight: ' . $stockComposition->weight . ', volume: ' . $stockComposition->volume);

                    if ($composition->cost) {
                        $averageComposition = $composition->cost->average * $composition->portion;
                        $costComposition = $averageComposition * $count;
                        $amountComposition = $costComposition * $item->count;

                        logs('documents')
                            ->info('Существует себестоимость c id: ' . $composition->cost->id);
                    } else {
                        $averageComposition = 0;
                        $costComposition = 0;
                        $amountComposition = 0;

                        logs('documents')
                            ->info('Себестоисмости нет, пишем нулевые значения');

                        $isWrong = 1;
                    }

                    $cost += $costComposition;
                    logs('documents')
                        ->info('Высчитываем себстоимость: ' . $count . ' * ' . $averageComposition . ' = ' . $cost);
//                                dd($composition);

                    $off = Off::create([
                        'document_id' => $item->document->id,
                        'document_type' => $documentModel,
                        'documents_item_id' => $item->id,
                        'documents_item_type' => $modelDocumentItem,
                        'cmv_id' => $composition->id,
                        'cmv_type' => $compositionModel,
                        'count' => $composition->portion * $count * $item->count,
                        'cost' => $costComposition,
                        'amount' => $amountComposition,
                        'stock_id' => $item->document->stock_id,
                    ]);

                    logs('documents')
                        ->info('Записали списание с id: ' . $off->id . ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

                    logs('documents')
                        ->info('=== КОНЕЦ СПИСАНИЯ ===');
                }
            }
        }
//      dd($cost);

        $result = [
            'cost' => $cost,
            'is_wrong' => $isWrong
        ];

        return $result;
    }

    /**
     * Списание со склада
     *
     * @param $item
     */
    public function off($item)
    {
        $documentModel = Entity::where('alias', $item->document->getTable())
            ->value('model');

        if ($item->document->getTable() == 'estimates') {
            $modelDocumentItem = $documentModel . 'sGoodsItem';
        } else {
            $modelDocumentItem = $documentModel . 'sItem';
        }

        logs('documents')
            ->info('=== СПИСАНИЕ ' . $item->getTable() . ' ' . $item->id . ' ===');

        // Списываем позицию состава
        $stockGeneral = Stock::find($item->document->stock_id);

        // Списываем позицию состава
        $product = $item->product;

        $productModel = Entity::where('alias', $product->getTable())
            ->value('model');

//      dd($stock_goods);
        $product->load([
           'stocks'
        ]);
        $stock = $product->stocks->where('stock_id', $stockGeneral->id)
            ->where('filial_id', $stockGeneral->filial_id)
            ->where('manufacturer_id', $product->article->manufacturer_id)
            ->first();
        if ($stock) {
            logs('documents')
                ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);

        } else {
            $user = auth()->user();

            // TODO - 15.10.19 - Какой ставить склад если при продаже нет склада товаров?

            $dataStock = [
                'cmv_id' => $product->id,
                'manufacturer_id' => $product->article->manufacturer_id,
                'stock_id' => $item->stock_id,
                'filial_id' => $item->document->filial_id,
            ];
            $entity_stock = Entity::where('alias', $product->getTable() . '_stocks')->first();
            $model_stock = $entity_stock->model;

            $stock = $model_stock::create($dataStock);

            logs('documents')
                ->info('Создан склад ' . $stock->getTable() . ' c id: ' . $stock->id);

        }

        logs('documents')
            ->info('Значения count: ' . $stock->count . ', reserve: ' . $stock->reserve . ', free: ' . $stock->free . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

        $item_count = $item->count;

        $stock->count -= $item_count;

        $item->load('reserve');
        if (optional($item->reserve)->count > 0) {
            if ($item->count == $item->reserve->count) {
                $stock->reserve -= $item->reserve->count;
                logs('documents')
                    ->info('Есть резерв с id: ' . $item->reserve->id . ', и количеством: ' . $item->reserve->count . ', списываем с резерва');
            } else {
                $dif = $item->count - $item->reserve->count;
                $stock->reserve -= $item->reserve->count;
                $stock->free -= $dif;
                logs('documents')
                    ->info('В пункте количество больше чем в резерве с id: ' . $item->reserve->id . ', списываем с резерва: ' . $item->reserve->count . ', и со свободных: ' . $dif . ', всего должно быть ' . $item->count);
            }

            $reserve = $item->reserve;

            $reserve->update([
                'count' => 0
            ]);
            logs('documents')
                ->info("Ставим резерву с id: $reserve->id значение количества 0");

        } else {
            $stock->free -= $item_count;
            logs('documents')
                ->info('Нет резерва, списываем со свободных');
        }

        if ($stock->count < 0 || $stock->free < 0) {
            logs('documents')
                ->info('Количество на складе < 0, ставим свободным 0');
            $stock->free = 0;
        }

        // TODO - 16.11.19 - Вес и обьем некорректно списываются если значение было 0

//        if ($stock->weight > 0) {
//            $stock->weight -= $product->weight * $item->count;
//        }
//        if ($stock->volume > 0) {
//            $stock->volume -= $product->volume * $item->count;
//        }
        $stock->weight -= $product->weight * $item->count;
        $stock->volume -= $product->volume * $item->count;

        $stock->save();

        logs('documents')
            ->info('Обновлены значения count: ' . $stock->count . ', reserve: ' . $stock->reserve . ', free: ' . $stock->free . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

        if ($product->cost) {
            $average_product = $product->cost->average * $product->portion;
            $cost_product = $average_product;
            $amount_product = $cost_product * $item->count;

            logs('documents')
                ->info('Существует себестоимость c id: ' . $product->cost->id);
        } else {
            $average_product = 0;
            $cost_product = 0;
            $amount_product = 0;

            logs('documents')
                ->info('Себестоисмости нет, пишем нулевые значения');

            $isWrong = 1;
        }

        $off = Off::create([
            'document_id' => $item->document->id,
            'document_type' => $documentModel,
            'documents_item_id' => $item->id,
            'documents_item_type' => $modelDocumentItem,
            'cmv_id' => $product->id,
            'cmv_type' => $productModel,
            'count' => $item->count,
            'cost' => $cost_product,
            'amount' => $amount_product,
            'stock_id' => $item->document->stock_id,
        ]);

        logs('documents')
            ->info('Записали списание с id: ' . $off->id . ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

        logs('documents')
            ->info('=== КОНЕЦ СПИСАНИЯ ===
                        ');
    }
}
