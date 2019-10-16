<?php

namespace App\Http\Controllers\Traits;

use App\Entity;
use App\Off;
use App\Stock;
use Illuminate\Support\Facades\Log;

trait Offable
{

    /**
     * Списывание состава со склада для производства.
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
                    $stock_production = $composition->stocks->filter(function ($stock, $key) {
                        return $stock->stock->is_production == 1;
                    });
//                    dd($stock_production);

                    if ($stock_production->isNotEmpty()) {
                        $stock_composition = $stock_production->first();

                        Log::channel('documents')
                            ->info('Существует склад ' . $stock_composition->getTable() . ' c id: ' . $stock_composition->id);

                    } else {
                        $data_stock = [
                            'cmv_id' => $item->cmv_id,
                            'manufacturer_id' => $item->cmv->article->manufacturer_id,
                            'stock_id' => $item->document->stock_id,
                            'filial_id' => $item->document->filial_id,
                            'is_produced' => true,
                        ];
                        $entity_composition_stock = Entity::where('alias', $relation_name.'_stocks')->first();
                        $model_composition_stock = 'App\\'.$entity_composition_stock->model;

                        $stock_composition = $model_composition_stock::create($data_stock);

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

    /**
     * Списание со склада.
     *
     * @param $item
     */

    public function off($item)
    {
        $entity_document = Entity::where('alias', $item->document->getTable())->first();
        $model_document = 'App\\' . $entity_document->model;

        $model_document_item = $model_document.'sItem';

        Log::channel('documents')
            ->info('=== СПИСАНИЕ ' . $item->getTable() . ' ' . $item->id . ' ===');

        // Списываем позицию состава
        $product = $item->product;
        $stock_goods = $product->stocks->filter(function ($stock, $key) {
            return $stock->stock->is_goods == 1;
        });
//      dd($stock_goods);

        if ($stock_goods->isNotEmpty()) {
            $stock = $stock_goods->first();

            Log::channel('documents')
                ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);

        } else {
            $user = \Auth::user();

            // TODO - 15.10.19 - Какой ставить склад если при продаже нет склада товаров?

            $data_stock = [
                'cmv_id' => $item->product_id,
                'manufacturer_id' => $item->product->article->manufacturer_id,
                'stock_id' => Stock::where('filial_id', $user->staff->first()->filial_id) ->fisrt()->id,
                'filial_id' => $user->staff->first()->filial_id,
            ];
            $entity_stock = Entity::where('alias', $product->getTable() . '_stocks')->first();
            $model_stock = 'App\\'.$entity_stock->model;

            $stock = $model_stock::create($data_stock);

            Log::channel('documents')
                ->info('Создан склад ' . $stock->getTable() . ' c id: ' . $stock->id);

        }

        Log::channel('documents')
            ->info('Значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

        $stock->count -= ($product->portion * $item->count);
        $stock->weight -= ($product->weight * $item->count);
        $stock->volume -= ($product->volume * $item->count);
        $stock->save();

        Log::channel('documents')
            ->info('Обновлены значения count: ' . $stock->count . ', weight: ' . $stock->weight . ', volume: ' . $stock->volume);

        $off = Off::create([
            'document_id' => $item->document->id,
            'document_type' => $model_document,
            'documents_item_id' => $item->id,
            'documents_item_type' => $model_document_item,
            'cmv_id' => $product->id,
            'cmv_type' => 'App\Goods',
            'count' => $product->portion * $item->count,
            'cost' => $item->price,
            'amount' => $item->count * $item->price,
            'stock_id' => $item->document->stock_id,
        ]);

        Log::channel('documents')
            ->info('Записали списание с id: ' . $off->id .  ', count: ' . $off->count . ', cost: ' . $off->cost . ', amount: ' . $off->amount);

        Log::channel('documents')
            ->info('=== КОНЕЦ СПИСАНИЯ ===
                        ');
    }
}
