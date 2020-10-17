<?php

namespace App\Http\Controllers\Traits;

use App\Cost;
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

        $modelStock = Entity::where('alias', $item->entity->alias . '_stocks')
            ->value('model');

        logs('documents')
            ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===');
        logs('documents')
            ->info("В документе выбран stock с id: {$item->document->stock_id}");

	    // Акутальный филиал
	    $stockGeneral = Stock::find($item->document->stock_id);
	    $filialId = $stockGeneral->filial_id;

        // Склад
        $stock = $item->cmv->stocks->where('stock_id', $stockGeneral->id)
            ->where('filial_id', $stockGeneral->filial_id)
            ->where('manufacturer_id', $item->manufacturer_id)
            ->first();
        if ($stock) {
            logs('documents')
                ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);
        } else {
            $dataStock = [
                'cmv_id' => $item->cmv_id,
                'manufacturer_id' => $item->manufacturer_id,
                'stock_id' => $stockGeneral->id,
                'filial_id' => $stockGeneral->filial_id,
            ];
            $stock = $modelStock::create($dataStock);

            logs('documents')
                ->info('Создан склад ' . $stock->getTable() . ' c id: ' . $stock->id);
        }


        $stocksCount = $modelStock::where([
            'filial_id' => $filialId,
            'cmv_id' => $item->cmv_id
        ])
        ->sum('count');

        logs('documents')
            ->info('Значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

        if ($item->cmv->article->package_status == 1) {
            $count = $item->count * $item->cmv->article->package_count;
            logs('documents')
                ->info('Принимаем в "' . $item->cmv->article->package_abbreviation . '": в количестве ' . $item->count . ', пересчитываем на ' . $item->cmv->article->unit->abbreviation . ' в количестве ' . $count);
        } else {
            $count = $item->count;
            logs('documents')
                ->info('Принимаем в стандартных ' . $item->cmv->article->unit->abbreviation . ' в количестве ' . $count);
        }

        $data = [
            'count' => $stock->count += $count,
            'weight' => $stock->weight += ($item->cmv->article->weight * $count),
            'volume' => $stock->volume += ($item->cmv->article->volume * $count),
        ];
        $stock->update($data);

        logs('documents')
            ->info('Обновлены значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

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
                ->info('Значения min: ' . $cost_item->min . ', max: ' . $cost_item->max . ', average: ' . $cost_item->average);

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
                ->info('Обновлены значения min: ' . $cost_item->min . ', max: ' . $cost_item->max . ', average: ' . $cost_item->average);

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
            'count' => $count,
            'cost' => $cost,
            'amount' => $count * $cost,
            'stock_id' => $item->document->stock_id,
        ]);

        logs('documents')
            ->info("Записано поступление с id: {$receipt->id}, count: {$receipt->count}, cost: {$receipt->cost}, amount: {$receipt->amount}");

        logs('documents')
            ->info('=== КОНЕЦ ПЕРЕБОРА ПУНКТА ===');
    }
}
