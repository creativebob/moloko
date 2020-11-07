<?php

namespace App\Http\Controllers\Traits;

use App\Cost;
use App\Models\System\Documents\ConsignmentsItem;
use App\Receipt;
use App\Entity;
use App\Stock;

trait Receiptable
{

    /**
     * Приходуем пункт документа
     *
     * @param $item
     * @param int $is_wrong
     */
    public function receipt($item, $isWrong = 0)
    {

        $modelStorage = Entity::where('alias', $item->entity->alias . '_stocks')
            ->value('model');

        logs('documents')
            ->info('=== ПРИХОДОВАНИЕ ' . $item->getTable() . ' ' . $item->id . ' ===');
        logs('documents')
            ->info("В документе выбран stock с id: {$item->document->stock_id}");

	    // Акутальный филиал
	    $stockGeneral = Stock::find($item->document->stock_id);
	    $filialId = $stockGeneral->filial_id;

        // хранилище
        $item->load([
            'cmv.stocks'
        ]);
        $storage = $item->cmv->stocks->where('stock_id', $stockGeneral->id)
            ->where('filial_id', $stockGeneral->filial_id)
            ->where('manufacturer_id', $item->manufacturer_id)
            ->first();
        if ($storage) {
            logs('documents')
                ->info('Существует хранилище ' . $storage->getTable() . ' c id: ' . $storage->id);
        } else {
            $dataStock = [
                'cmv_id' => $item->cmv_id,
                'manufacturer_id' => $item->manufacturer_id,
                'stock_id' => $stockGeneral->id,
                'filial_id' => $stockGeneral->filial_id,
            ];
            $storage = $modelStorage::create($dataStock);

            logs('documents')
                ->info('Создано хранилище ' . $storage->getTable() . ' c id: ' . $storage->id);
        }


        $stocksCount = $modelStorage::where([
            'filial_id' => $filialId,
            'cmv_id' => $item->cmv_id
        ])
        ->sum('count');

        logs('documents')
            ->info("Значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

        if ($item->cmv->article->package_status == 1) {
            $count = $item->count * $item->cmv->article->package_count;
            logs('documents')
                ->info('Принимаем в "' . $item->cmv->article->package_abbreviation . '": в количестве ' . $item->count . ', пересчитываем на ' . $item->cmv->article->unit->abbreviation . ' в количестве ' . $count);
        } else {
            $count = $item->count;
            logs('documents')
                ->info('Принимаем в стандартных ' . $item->cmv->article->unit->abbreviation . ' в количестве ' . $count);
        }

        $newCount = $storage->count += $count;

        $data = [
            'count' => $newCount,
            'free' => ($newCount > 0) ? ($newCount - $storage->reserve) : 0,
            'weight' => $storage->weight += ($item->cmv->article->weight * $count),
            'volume' => $storage->volume += ($item->cmv->article->volume * $count),
        ];
        $storage->update($data);

        logs('documents')
            ->info("Обновлены значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

        // Себестоимость

        if ($item->cmv->article->package_status == 1) {
            $cost = $item->cost / $item->cmv->article->package_count;
            logs('documents')
                ->info('Принимаем в "' . $item->cmv->article->package_abbreviation . '": в количестве ' . $item->count . ', пересчитываем себестоимость: ' . $cost . ' за 1 ' . $item->cmv->article->unit->abbreviation);
        } else {
            $cost = $item->cost;
            logs('documents')
                ->info('Принимаем в стандартных ' . $item->cmv->article->unit->abbreviation . ' в количестве ' . $count . ', себестоимость: ' . $cost);
        }

        if ($item->cmv->cost) {
            $cost_item = $item->cmv->cost;
//						dd($cost_item);

            logs('documents')
                ->info('Существует себестоимость c id: ' . $cost_item->id);
            logs('documents')
                ->info("Значения min: {$cost_item->min}, max: {$cost_item->max} , average: {$cost_item->average}");

            $cost_average = $cost_item->average;
            if ($stocksCount > 0) {
                $average = (($stocksCount * $cost_average) + ($count * $cost)) / ($stocksCount + $count);
            } else {
                $average = $cost;
            };

            if (is_null($cost_item->min) || is_null($cost_item->max)) {
                $data_cost = [
                    'min' => $cost,
                    'max' => $cost,
                    'average' => $cost,
                ];

            } else {
                $data_cost = [
                    'min' => ($cost < $cost_item->min) ? $cost : $cost_item->min,
                    'max' => ($cost > $cost_item->max) ? $cost : $cost_item->max,
                    'average' => $average
                ];
            }

//			dd($data_cost);

            $cost_item->update($data_cost);

            logs('documents')
                ->info("Обновлены значения min: {$cost_item->min}, max: {$cost_item->max} , average: {$cost_item->average}");

        } else {
            $data_cost = [
                'cmv_id' => $item->cmv_id,
                'cmv_type' => $item->cmv_type,
                'manufacturer_id' => $item->manufacturer_id,
                'min' => $cost,
                'max' => $cost,
                'average' => $cost,
	            'filial_id' => $filialId,
                'is_wrong' => $isWrong
            ];
//			dd($data_cost);

            $cost_item = Cost::create($data_cost);
//			dd($cost_item);

            logs('documents')
                ->info('Создана себестоимость c id: ' . $cost_item->id);
            logs('documents')
                ->info('Значения min: ' . $cost_item->min . ', max: ' . $cost_item->max . ', average: ' . $cost_item->average);

        }


        $modelDocument = Entity::where('alias', $item->document->getTable())
            ->value('model');
        $modelDocumentItem = "{$modelDocument}sItem";

        $modelItem = $item->entity->model;

        $receipt = Receipt::create([
            'document_id' => $item->document->id,
            'document_type' => $modelDocument,

            'documents_item_id' => $item->id,
            'documents_item_type' => $modelDocumentItem,

            'cmv_id' => $item->cmv->id,
            'cmv_type' => $modelItem,

            'storage_id' => $storage->id,
            'storage_type' => $modelStorage,

            'count' => $count,
            'cost' => $cost,
            'amount' => $count * $cost,

            'stock_id' => $item->document->stock_id,
        ]);

        logs('documents')
            ->info("Записано поступление с id: {$receipt->id}, count: {$receipt->count}, cost: {$receipt->cost}, amount: {$receipt->amount}");

        logs('documents')
            ->info('=== КОНЕЦ ПРИХОДОВАНИЯ ===
            ');
    }

    /**
     * Отмена приходования пункта документа
     *
     * @param $receipt
     */
    public function unreceipt($receipt)
    {

        dd($receipt);

        logs('documents')
            ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $receipt->documents_item->getTable() . ' ' . $receipt->documents_item->id . ' ===');

        // хранилище
        $storage = $receipt->storage;
//        dd($storage);

        logs('documents')
            ->info('Существует хранилище ' . $storage->getTable() . ' c id: ' . $storage->id);

        $storage_count = $storage->count;

        logs('documents')
            ->info('Значения count: ' . $storage->count . ', weight: ' . $storage->weight . ', volume: ' . $storage->volume);

        $storage->count -= $receipt->count;
        $storage->weight -= ($receipt->cmv->article->weight * $receipt->count);
        $storage->volume -= ($receipt->cmv->article->volume * $receipt->count);
        $storage->free -= $receipt->count;
        $storage->save();

        logs('documents')
            ->info('Обновлены значения count: ' . $storage->count . ', weight: ' . $storage->weight . ', volume: ' . $storage->volume);

        // Себестоимость
        $cost = $receipt->cmv->cost;

        logs('documents')
            ->info('Существует себестоимость c id: ' . $cost->id);
        logs('documents')
            ->info('Значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);

        // Получаем из сессии необходимые данные
        $answer = operator_right('consignments_items', true, 'index');

        $min = ConsignmentsItem::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->where([
                'cmv_id' => $receipt->cmv_id,
                'cmv_type' => $receipt->cmv_type,
            ])
            ->whereHas('consignment', function ($q) use ($receipt) {
                $q->whereNotNull('receipted_at')
                    ->where('id', '!=', $receipt->document_id);
            })
            ->min('cost');
//					dd($min);

        $max = ConsignmentsItem::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->where([
                'cmv_id' => $item->cmv_id,
                'cmv_type' => $item->cmv_type,
            ])
            ->whereHas('consignment', function ($q) use ($item) {
                $q->whereNotNull('receipted_at')
                    ->where('id', '!=', $item->document->id);
            })
            ->max('cost');
//					dd($max);

        if (is_null($min) || is_null($max)) {
            $average = 0;
            $min = 0;
            $max = 0;
        } else {
            $average = (($storage_count * $cost->average) - ($item->count * $item->price)) / $storage->count;
        }
//        dd($average);
        $cost->update([
            'min' => $min,
            'max' => $max,
            'average' => $average,
        ]);
//					dd($cost);

        $item->receipt()->forceDelete();

        logs('documents')
            ->info('Обновлены значения min: ' . $cost->min . ', max: ' . $cost->max . ', average: ' . $cost->average);

        logs('documents')
            ->info('=== КОНЕЦ ПЕРЕБОРА ПУНКТА ===
                        ');

    }
}
