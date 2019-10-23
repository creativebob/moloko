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
     * @return array
     */

    public function production($item)
    {
        $cost = 0;
        $is_wrong = 0;

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
                    $stock_general = Stock::findOrFail($item->document->stock_id);
//                    dd($stock_production);

                    if ($composition->stocks->where('stock_id', $stock_general->id)->where('filial_id', $stock_general->filial_id)->where('manufacturer_id', $composition->article->manufacturer_id)->first()) {
                        $stock_composition = $composition->stocks->where('stock_id', $stock_general->id)->where('filial_id', $stock_general->filial_id)->where('manufacturer_id', $composition->article->manufacturer_id)->first();

                        Log::channel('documents')
                            ->info('Существует склад ' . $stock_composition->getTable() . ' c id: ' . $stock_composition->id);

                    } else {
                        $data_stock = [
                            'cmv_id' => $composition->id,
                            'manufacturer_id' => $composition->article->manufacturer_id,
                            'stock_id' => $item->document->stock_id,
                            'filial_id' => $item->document->filial_id,
                            'is_produced' => true,
                        ];
                        $entity_composition_stock = Entity::where('alias', $relation_name.'_stocks')->first();
                        $model_composition_stock = 'App\\'.$entity_composition_stock->model;

                        $stock_composition = $model_composition_stock::create($data_stock);

                        Log::channel('documents')
                            ->info('Создан склад ' . $stock_composition->getTable() . ' c id: ' . $stock_composition->id);

                        $is_wrong = 1;
                    }

                    Log::channel('documents')
                        ->info('Значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

                    // Получаем себестоимость
                    $count = $composition->pivot->value;

                    $stock_composition->count -= ($composition->portion * $count * $item->count);
                    $stock_composition->weight -= ($composition->weight * $count * $item->count);
                    $stock_composition->volume -= ($composition->volume * $count * $item->count);
                    $stock_composition->save();

                    Log::channel('documents')
                        ->info('Обновлены значения count: ' . $stock_composition->count . ', weight: ' . $stock_composition->weight . ', volume: ' . $stock_composition->volume);

                    if ($composition->cost) {
                        $average_composition = $composition->cost->average * $composition->portion;
                        $cost_composition = $average_composition * $count;
                        $amount_composition = $cost_composition * $item->count;

                        Log::channel('documents')
                            ->info('Существует себестоимость c id: ' . $composition->cost->id);
                    } else {
                        $average_composition = 0;
                        $cost_composition = 0;
                        $amount_composition = 0;

                        Log::channel('documents')
                            ->info('Себестоисмости нет, пишем нулевые значения');

                        $is_wrong = 1;
                    }

                    $cost += $cost_composition;
                    Log::channel('documents')
                        ->info('Высчитываем себстоимость: ' . $count . ' * ' . $average_composition . ' = ' . $cost);
//                                dd($composition);

                    $off = Off::create([
                        'document_id' => $item->document->id,
                        'document_type' => $model_document,
                        'documents_item_id' => $item->id,
                        'documents_item_type' => $model_document_item,
                        'cmv_id' => $composition->id,
                        'cmv_type' => $model_composition,
                        'count' => $composition->portion * $count * $item->count,
                        'cost' => $cost_composition,
                        'amount' => $amount_composition,
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

        $result = [
            'cost' => $cost,
            'is_wrong' => $is_wrong
        ];

        return $result;
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
