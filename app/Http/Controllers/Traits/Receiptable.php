<?php

namespace App\Http\Controllers\Traits;

use App\Cost;
use App\Receipt;
use App\Entity;

use Illuminate\Support\Facades\Log;

trait Receiptable
{

    /**
     * Прихожуем пункт документа.
     *
     * @param $item
     */

    public function receipt($item)
    {

        $entity_document = Entity::where('alias', $item->document->getTable())->first();
        $model_document = 'App\\' . $entity_document->model;

        $model_document_item = $model_document.'sItem';

        $model_item = 'App\\' . $item->entity->model;

        $entity_stock = Entity::where('alias', $item->entity->alias . '_stocks')->first();
        $model_stock = 'App\\' . $entity_stock->model;

        Log::channel('documents')
            ->info('=== ПЕРЕБИРАЕМ ПУНКТ ' . $item->getTable() . ' ' . $item->id . ' ===');

        Log::channel('documents')
            ->info('В документе выбран stock с id: ' . $item->document->stock_id
            );

        // Склад
        if ($item->cmv->stocks->firstWhere('stock_id', $item->document->stock_id)) {
            $stock = $item->cmv->stocks->firstWhere('stock_id', $item->document->stock_id);

            Log::channel('documents')
                ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);

        } else {
            $data_stock = [
                'cmv_id' => $item->cmv_id,
                'manufacturer_id' => $item->cmv->article->manufacturer_id,
                'stock_id' => $item->document->stock_id,
                'filial_id' => $item->document->filial_id,
            ];
            $stock = (new $model_stock())->create($data_stock);

            Log::channel('documents')
                ->info('Создан склад ' . $stock->getTable() . ' c id: ' . $stock->id);

        }

        $stock_count = $stock->count;

        Log::channel('documents')
            ->info('Значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

        if ($item->cmv->article->package_status == 1) {
            $count = $item->count * $item->cmv->article->package_count;
            Log::channel('documents')
                ->info('Принимаем в "' . $item->cmv->article->package_abbreviation . '": в количестве ' . $item->count . ', пересчитываем на ' . $item->cmv->article->unit->abbreviation . ' в количестве ' . $count);
        } else {
            $count = $item->count;
            Log::channel('documents')
                ->info('Принимаем в стандартных ' . $item->cmv->article->unit->abbreviation . ' в количестве ' . $count);
        }

        $stock->count += $count;
        $stock->weight += ($item->cmv->article->weight * $count);
        $stock->volume += ($item->cmv->article->volume * $count);
        $stock->save();

        Log::channel('documents')
            ->info('Обновлены значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

        // Себестоимость

        if ($item->cmv->article->package_status == 1) {
            $cost = $item->cost / $item->cmv->article->package_count;
            Log::channel('documents')
                ->info('Принимаем в "' . $item->cmv->article->package_abbreviation . '": в количестве ' . $item->count . ', пересчитываем себестоимость: ' . $cost . ' за 1 ' . $item->cmv->article->unit->abbreviation);
        } else {
            $cost = $item->cost;
            Log::channel('documents')
                ->info('Принимаем в стандартных ' . $item->cmv->article->unit->abbreviation . ' в количестве ' . $count . ', себестоимость: ' . $cost);
        }

        if ($item->cmv->cost) {
            $cost_item = $item->cmv->cost;
//						dd($cost_item);

            Log::channel('documents')
                ->info('Существует себестоимость c id: ' . $cost_item->id);
            Log::channel('documents')
                ->info('Значения min: ' . $cost_item->min . ', max: ' . $cost_item->max . ', average: ' . $cost_item->average);

            $cost_average = $cost_item->average;
            if ($stock->count > 0) {
                $average = (($stock_count * $cost_average) + ($count * $cost)) / $stock->count;
            } else {
                $average = (($stock_count * $cost_average) + ($count * $cost));
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

            Log::channel('documents')
                ->info('Обновлены значения min: ' . $cost_item->min . ', max: ' . $cost_item->max . ', average: ' . $cost_item->average);

        } else {
            $data_cost = [
                'cmv_id' => $item->cmv_id,
                'cmv_type' => $item->cmv_type,
                'manufacturer_id' => $item->cmv->article->manufacturer_id,
                'min' => $cost,
                'max' => $cost,
                'average' => $cost,
            ];
//						dd($data_cost);
            $cost_item = Cost::create($data_cost);
//						dd($cost_item);

            Log::channel('documents')
                ->info('Создана себестоимость c id: ' . $cost_item->id);
            Log::channel('documents')
                ->info('Значения min: ' . $cost_item->min . ', max: ' . $cost_item->max . ', average: ' . $cost_item->average);


        }


        $receipt = Receipt::create([
            'document_id' => $item->document->id,
            'document_type' => $model_document,
            'documents_item_id' => $item->id,
            'documents_item_type' => $model_document_item,
            'cmv_id' => $item->cmv->id,
            'cmv_type' => $model_item,
            'count' => $count,
            'cost' => $cost,
            'amount' => $count * $cost,
            'stock_id' => $item->document->stock_id,
        ]);

        Log::channel('documents')
            ->info('Записано поступление с id: ' . $receipt->id . ', count: ' . $receipt->count . ', cost: ' . $receipt->cost . ', amount: ' . $receipt->amount);

        Log::channel('documents')
            ->info('=== КОНЕЦ ПЕРЕБОРА ПУНКТА ===
                        ');
    }
}