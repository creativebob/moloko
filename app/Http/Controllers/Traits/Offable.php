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

        $modelDocument = Entity::where('alias', $item->document->getTable())
            ->value('model');

        $modelDocumentItem = Entity::where('alias', $item->getTable())
            ->value('model');

        $relationsNames = [
            'raws',
            'containers',
            'attachments'
        ];

        // Не набор
        if ($item->cmv->article->kit == 0) {
            logs('documents')
                ->info('Производим не набор');

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
                            $modelStorage = Entity::where('alias', $composition->getTable() . '_stocks')
                                ->value('model');

                            $storage = $modelStorage::create($dataStock);

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

                        $modelStorage = Entity::where('alias', $storage->getTable())
                            ->value('model');

                        $off = Off::create([
                            'document_id' => $item->document->id,
                            'document_type' => $modelDocument,

                            'documents_item_id' => $item->id,
                            'documents_item_type' => $modelDocumentItem,

                            'cmv_id' => $composition->id,
                            'cmv_type' => $compositionModel,

                            'storage_id' => $storage->id,
                            'storage_type' => $modelStorage,

                            'count' => $composition->portion * $count * $item->count,

                            'weight_unit' => $composition->weight,
                            'volume_unit' => $composition->volume,

                            'cost_unit' => $costComposition,
                            'total' => $amountComposition,

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
        } else {
            // Набор
            logs('documents')
                ->info('Производим набор');
            foreach($item->cmv->article->goods as $curGoods) {
                foreach ($relationsNames as $relationName) {
                    if ($curGoods->article->$relationName->isNotEmpty()) {

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
                                $modelStorage = Entity::where('alias', $composition->getTable() . '_stocks')
                                    ->value('model');

                                $storage = $modelStorage::create($dataStock);

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

                            $modelStorage = Entity::where('alias', $storage->getTable())
                                ->value('model');

                            $off = Off::create([
                                'document_id' => $item->document->id,
                                'document_type' => $modelDocument,

                                'documents_item_id' => $item->id,
                                'documents_item_type' => $modelDocumentItem,

                                'cmv_id' => $composition->id,
                                'cmv_type' => $compositionModel,

                                'storage_id' => $storage->id,
                                'storage_type' => $modelStorage,

                                'count' => $composition->portion * $count * $item->count,

                                'weight_unit' => $composition->weight,
                                'volume_unit' => $composition->volume,

                                'cost_unit' => $costComposition,
                                'total' => $amountComposition,

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
     * Списание с хранилица
     *
     * @param $item
     */
    public function off($item)
    {

        logs('documents')
            ->info('=== СПИСАНИЕ ' . $item->getTable() . ' ' . $item->id . ' ===');

        $stockGeneral = Stock::find($item->stock_id);
        $cmv = $item->cmv;
        $cmv->load([
           'stocks'
        ]);

        $storage = $cmv->stocks->where('stock_id', $stockGeneral->id)
            ->where('filial_id', $stockGeneral->filial_id)
            ->where('manufacturer_id', $cmv->article->manufacturer_id)
            ->first();
        if ($storage) {
            logs('documents')
                ->info('Существует хранилище ' . $storage->getTable() . ' c id: ' . $storage->id);

        } else {
            // TODO - 15.10.19 - Какой ставить склад если при продаже нет склада товаров?
            $dataStock = [
                'cmv_id' => $cmv->id,
                'manufacturer_id' => $cmv->article->manufacturer_id,
                'stock_id' => $item->stock_id,
                'filial_id' => $item->document->filial_id,
            ];

            $modelStorage = Entity::where('alias', $cmv->getTable() . '_stocks')
                ->value('model');

            $storage = $modelStorage::create($dataStock);

            logs('documents')
                ->info('Создано хранилище ' . $storage->getTable() . ' c id: ' . $storage->id);
        }

        logs('documents')
            ->info("Значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

        $itemCount = $item->count;

        $newCount = $storage->count - $itemCount;
        $reserveCount = $storage->reserve;
        $free = $storage->free;

        $item->load('reserve');
        if (optional($item->reserve)->count > 0) {
            if ($item->count == $item->reserve->count) {
                $reserveCount = $storage->reserve - $item->reserve->count;
                logs('documents')
                    ->info('Есть резерв с id: ' . $item->reserve->id . ', и количеством: ' . $item->reserve->count . ', списываем с резерва');
            } else {
                $dif = $item->count - $item->reserve->count;
                $reserveCount = $storage->reserve - $item->reserve->count;
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
                ->info('Количество на хранилище < 0, ставим свободным 0');
            $free = 0;
        }

        // TODO - 16.11.19 - Вес и обьем некорректно списываются если значение было 0

//        if ($storage->weight > 0) {
//            $storage->weight -= $cmv->weight * $item->count;
//        }
//        if ($storage->volume > 0) {
//            $storage->volume -= $cmv->volume * $item->count;
//        }

        $data = [
            'count' => $newCount,
            'reserve' => $reserveCount,
            'free' => $free,
            'weight' => $storage->weight -= ($cmv->weight * $item->count),
            'volume' => $storage->volume -= ($cmv->volume * $item->count),
        ];
        $storage->update($data);

        logs('documents')
            ->info("Обновлены значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

        if ($cmv->cost) {
            $average_cmv = $cmv->cost->average * $cmv->portion;
            $cost_cmv = $average_cmv;
            $amount_cmv = $cost_cmv * $item->count;

            logs('documents')
                ->info('Существует себестоимость c id: ' . $cmv->cost->id);
        } else {
            $average_cmv = 0;
            $cost_cmv = 0;
            $amount_cmv = 0;

            logs('documents')
                ->info('Себестоисмости нет, пишем нулевые значения');

            $isWrong = 1;
        }

        $modelDocument = Entity::where('alias', $item->document->getTable())
            ->value('model');

        $modelDocumentItem = Entity::where('alias', $item->getTable())
            ->value('model');

        $modelCmv = Entity::where('alias', $cmv->getTable())
            ->value('model');

        $modelStorage = Entity::where('alias', $storage->getTable())
            ->value('model');

        $off = Off::create([
            'document_id' => $item->document->id,
            'document_type' => $modelDocument,

            'documents_item_id' => $item->id,
            'documents_item_type' => $modelDocumentItem,

            'cmv_id' => $cmv->id,
            'cmv_type' => $modelCmv,

            'storage_id' => $storage->id,
            'storage_type' => $modelStorage,

            'weight_unit' => $item->weight,
            'volume_unit' => $item->volume,

            'cost_unit' => $cost_cmv,
            'total' => $amount_cmv,

            'stock_id' => $item->stock_id,
        ]);

        logs('documents')
            ->info('Записали списание с id: ' . $off->id . ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

        logs('documents')
            ->info('=== КОНЕЦ СПИСАНИЯ ===
                        ');
    }
}
