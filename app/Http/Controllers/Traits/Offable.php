<?php

namespace App\Http\Controllers\Traits;

use App\Entity;
use App\Off;
use Illuminate\Support\Facades\Log;

trait Offable
{

    /**
     * Прихожуем пункт документа.
     *
     * @param $item
     * @return int
     */

    public function production($item)
    {
        $cost = 0;
        $relations = [
            'raws',
            'containers',
            'attachments'
        ];

        $entity_document = Entity::where('alias', $item->document->getTable())->first();
        $model_document = 'App\\' . $entity_document->model;

        $model_document_item = $model_document.'sItem';

        foreach ($relations as $relation_name) {
            if ($item->cmv->article->$relation_name->isNotEmpty()) {

                $entity_composition = Entity::where('alias', $relation_name)->first();
                $model_composition = 'App\\'.$entity_composition->model;

                foreach ($item->cmv->article->$relation_name as $composition) {

                    Log::channel('documents')
                        ->info('=== СПИСАНИЕ ' . $composition->getTable() . ' ' . $composition->id . ' ===');

                    // Списываем позицию состава
                    if ($composition->stock) {
                        $stock_composition = $composition->stock;

                        Log::channel('documents')
                            ->info('Существует склад ' . $stock_composition->getTable() . ' c id: ' . $stock_composition->id);

                    } else {
                        $data_stock = [
                            'cmv_id' => $item->cmv_id,
                            'manufacturer_id' => $item->cmv->article->manufacturer_id,
                            'stock_id' => $item->document->stock_id,
                            'filial_id' => $item->document->filial_id,
                        ];
                        $entity_composition_stock = Entity::where('alias', $relation_name.'_stocks')->first();
                        $model_composition_stock = 'App\\'.$entity_composition_stock->model;

                        $stock_composition = (new $model_composition_stock())->create($data_stock);

                        Log::channel('documents')
                            ->info('Создан склад ' . $stock_composition->getTable() . ' c id: ' . $stock_composition->id);

                    }

                    Log::channel('documents')
                        ->info('Значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

                    // Получаем себестоимость
                    $count = $composition->pivot->value;
                    $cost += ($count * $composition->cost->average);

                    $stock_composition->count -= ($composition->portion * $count * $item->count);
                    $stock_composition->weight -= ($composition->weight * $count * $item->count);
                    $stock_composition->volume -= ($composition->volume * $count * $item->count);
                    $stock_composition->save();

                    Log::channel('documents')
                        ->info('Обновлены значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

//                                dd($composition);
                    $off = Off::create([
                        'document_id' => $item->document->id,
                        'document_type' => $model_document,
                        'documents_item_id' => $item->id,
                        'documents_item_type' => $model_document_item,
                        'cmv_id' => $composition->id,
                        'cmv_type' => $model_composition,
                        'count' => $composition->portion * $count * $item->count,
                        'cost' => $composition->cost->average,
                        'amount' => ($count * $item->count) * $composition->cost->average,
                        'stock_id' => $item->document->stock_id,
                    ]);

                    Log::channel('documents')
                        ->info('Записали списание с id: ' . $off->id .  ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

                    Log::channel('documents')
                        ->info('=== КОНЕЦ СПИСАНИЯ ===
		                            ');
                }
            }
        }
//      dd($cost);

        return $cost;
    }
}