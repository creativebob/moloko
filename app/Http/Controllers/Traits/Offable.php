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
                    $storage = $composition->stocks->where('stock_id', $stockGeneral->id)
                        ->where('filial_id', $stockGeneral->filial_id)
                        ->where('manufacturer_id', $composition->article->manufacturer_id)
                        ->first();
                    if ($storage) {
                        logs('documents')
                            ->info('Существует склад ' . $storage->getTable() . ' c id: ' . $storage->id);
                    } else {
                        $dataStock = [
                            'cmv_id' => $composition->id,
                            'manufacturer_id' => $composition->article->manufacturer_id,
                            'stock_id' => $item->document->stock_id,
                            'filial_id' => $item->document->filial_id,
                        ];
                        $storageModel = Entity::where('alias', $storage->getTable())
                            ->value('model');

                        $storage = $storageModel::create($dataStock);

                        logs('documents')
                            ->info('Создан склад ' . $storage->getTable() . ' c id: ' . $storage->id);

                        $isWrong = 1;
                    }

                    logs('documents')
                        ->info('Значения count: ' . $storage->count . ', weight: ' . $storage->weight . ', volume: ' . $storage->volume);

                    // Получаем себестоимость
                    $count = $composition->pivot->value;

                    $newCount = $storage->count -= ($composition->portion * $count * $item->count);

                    $data = [
                        'count' => $newCount,
                        'free' => $newCount > 0 ? $newCount : 0,
                        'weight' => $storage->weight -= ($composition->weight * $count * $item->count),
                        'volume' => $storage->volume -= ($composition->volume * $count * $item->count)
                    ];
                    $storage->update($data);

                    logs('documents')
                        ->info('Обновлены значения count: ' . $storage->count . ', weight: ' . $storage->weight . ', volume: ' . $storage->volume);

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

                    $storageModel = Entity::where('alias', $storage->getTable())
                        ->value('model');

                    $off = Off::create([
                        'document_id' => $item->document->id,
                        'document_type' => $documentModel,

                        'documents_item_id' => $item->id,
                        'documents_item_type' => $modelDocumentItem,

                        'cmv_id' => $composition->id,
                        'cmv_type' => $compositionModel,

                        'storage_id' => $storage->id,
                        'storage_type' => $storageModel,

                        'count' => $composition->portion * $count * $item->count,
                        'cost' => $costComposition,

                        'amount' => $amountComposition,
                        'stock_id' => $item->document->stock_id,
                    ]);

                    logs('documents')
                        ->info('Записали списание с id: ' . $off->id . ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

                    logs('documents')
                        ->info('=== КОНЕЦ СПИСАНИЯ ===
                        ');
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

        // TODO - 05.11.20 - Завести сущности для получения моделей и избавленяи от хардкода
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
        $storage = $product->stocks->where('stock_id', $stockGeneral->id)
            ->where('filial_id', $stockGeneral->filial_id)
            ->where('manufacturer_id', $product->article->manufacturer_id)
            ->first();
        if ($storage) {
            logs('documents')
                ->info('Существует склад ' . $storage->getTable() . ' c id: ' . $storage->id);

        } else {
            $user = auth()->user();

            // TODO - 15.10.19 - Какой ставить склад если при продаже нет склада товаров?

            $dataStock = [
                'cmv_id' => $product->id,
                'manufacturer_id' => $product->article->manufacturer_id,
                'stock_id' => $item->stock_id,
                'filial_id' => $item->document->filial_id,
            ];

            $storageModel = Entity::where('alias', $product->getTable() . '_stocks')
                ->value('model');

            $storage = $storageModel::create($dataStock);

            logs('documents')
                ->info('Создан склад ' . $storage->getTable() . ' c id: ' . $storage->id);

        }

        logs('documents')
            ->info("Значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

        $itemCount = $item->count;

        $newCount = $storage->count - $itemCount;
        $reserve = $storage->reserve;
        $free = $storage->free;

        $item->load('reserve');
        if (optional($item->reserve)->count > 0) {
            if ($item->count == $item->reserve->count) {
                $reserve = $storage->reserve - $item->reserve->count;
                logs('documents')
                    ->info('Есть резерв с id: ' . $item->reserve->id . ', и количеством: ' . $item->reserve->count . ', списываем с резерва');
            } else {
                $dif = $item->count - $item->reserve->count;
                $reserve = $storage->reserve - $item->reserve->count;
                $free = $storage->free - $dif;
                logs('documents')
                    ->info('В пункте количество больше чем в резерве с id: ' . $item->reserve->id . ', списываем с резерва: ' . $item->reserve->count . ', и со свободных: ' . $dif . ', всего должно быть ' . $item->count);
            }

            $reserve = $item->reserve;

            $reserve->update([
                'count' => 0
            ]);
            logs('documents')
                ->info("Ставим резерву с id: {$reserve->id} значение количества 0");

        } else {
            $free = $storage->free - $itemCount;
            logs('documents')
                ->info('Нет резерва, списываем со свободных');
        }

        if ($newCount < 0 || $free < 0) {
            logs('documents')
                ->info('Количество на складе < 0, ставим свободным 0');
            $free = 0;
        }

        // TODO - 16.11.19 - Вес и обьем некорректно списываются если значение было 0

//        if ($storage->weight > 0) {
//            $storage->weight -= $product->weight * $item->count;
//        }
//        if ($storage->volume > 0) {
//            $storage->volume -= $product->volume * $item->count;
//        }

        $data = [
            'count' => $newCount,
            'reserve' => $reserve,
            'free' => $free,
            'weight' => $storage->weight -= ($product->weight * $item->count),
            'volume' => $storage->volume -= ($product->volume * $item->count),
        ];
        $storage->update($data);

        logs('documents')
            ->info("Обновлены значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

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

        $storageModel = Entity::where('alias', $storage->getTable())
            ->value('model');

        $off = Off::create([
            'document_id' => $item->document->id,
            'document_type' => $documentModel,

            'documents_item_id' => $item->id,
            'documents_item_type' => $modelDocumentItem,

            'cmv_id' => $product->id,
            'cmv_type' => $productModel,

            'storage_id' => $storage->id,
            'storage_type' => $storageModel,

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
