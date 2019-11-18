<?php

namespace App\Http\Controllers\Traits;

use App\Entity;
use App\Reserve;
use App\ReservesHistory;
use App\Stock;
use Illuminate\Support\Facades\Log;

trait Reservable
{

    /**
     * Резервирование на складе.
     *
     * @param $item
     */

    public function reserve($item)
    {
        $entity_document = Entity::where('alias', $item->document->getTable())->first();
        $model_document = 'App\\' . $entity_document->model;

        if ($item->document->getTable() == 'estimates') {
            $model_document_item = $model_document.'sGoodsItem';
        } else {
            $model_document_item = $model_document.'sItem';
        }

        Log::channel('documents')
            ->info('=== РЕЗЕРВИРОВАНИЕ ' . $item->getTable() . ' ' . $item->id . ' ===');

        // Списываем позицию состава
        $stock_general = Stock::findOrFail($item->stock_id);

        // Списываем позицию состава
        $product = $item->product;

        $entity_product = Entity::where('alias', $product->getTable())->first();
        $model_product = 'App\\'.$entity_product->model;

//      dd($stock_goods);

        $stock = $product->stocks->where('stock_id', $stock_general->id)->where('filial_id', $stock_general->filial_id)->where('manufacturer_id', $product->article->manufacturer_id)->first();
        if ($stock) {

            Log::channel('documents')
                ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);

            Log::channel('documents')
                ->info('Значения count: ' . $stock->count . ', reserve: ' . $stock->reserve . ', free: ' . $stock->free);

            $item_count = $item->count;

            if ($stock->free > 0) {

                if ($item_count > $stock->free) {
                    $result = 'Резерв поставлен не на все количество, недостаточно ' . ($item_count - $stock->free);

                    $item_count = $stock->free;

                    $stock->free -= $item_count;
                    $stock->reserve += $item_count;
                } else {
                    $result = 'Резерв успешно поставлен';
                    $stock->free -= $item->count;
                    $stock->reserve += $item->count;
                }

                $stock->save();

                Log::channel('documents')
                    ->info('Обновлены значения count: ' . $stock->count . ', reserve: ' . $stock->reserve . ', free: ' . $stock->free);

                if ($item->reserve) {
                    $reserve = $item->reserve;
                    $reserve->count += $item_count;
                    $reserve->save();


                    $reserve->history()->save(
                        ReservesHistory::make([
                            'count' => $item_count
                        ])
                    );

                    Log::channel('documents')
                        ->info('Обновили актуальный резерв с id: ' . $reserve->id .  ', count: ' . $reserve->count);

                } else {
                    $reserve = Reserve::create([
                        'document_id' => $item->document->id,
                        'document_type' => $model_document,
                        'documents_item_id' => $item->id,
                        'documents_item_type' => $model_document_item,
                        'cmv_id' => $product->id,
                        'cmv_type' => $model_product,
                        'count' => $item_count,
                        'stock_id' => $item->document->stock_id,
                        'filial_id' => $item->document->filial_id,
                    ]);

                    Log::channel('documents')
                        ->info('Записали актуальный резерв с id: ' . $reserve->id .  ', count: ' . $reserve->count);
                }

                $item->update([
                   'is_reserved' => true
                ]);

                Log::channel('documents')
                    ->info('=== КОНЕЦ РЕЗЕРВИРОВАНИЯ ===
                        ');
            } else {
                Log::channel('documents')
                    ->info('На сладе свободных остатков нет');
                $result = 'На сладе свободных остатков нет';
            }
        } else {
            Log::channel('documents')
                ->info('Склада нет, негде ставить в резерв');
            $result = 'Не существует склада товара, невозможно поставить в резерв';
        }


        return $result;
    }

    /**
     * Отмена резервирования на складе.
     *
     * @param $item
     */

    public function unreserve($item)
    {

        Log::channel('documents')
            ->info('=== ОТМЕНА РЕЗЕРВИРОВАНИЯ ' . $item->getTable() . ' ' . $item->id . ' ===');

        $stock_general = Stock::findOrFail($item->stock_id);

        $product = $item->product;

        $stock = $product->stocks->where('stock_id', $stock_general->id)->where('filial_id', $stock_general->filial_id)->where('manufacturer_id', $product->article->manufacturer_id)->first();
        if ($stock) {

            Log::channel('documents')
                ->info('Существует склад ' . $stock->getTable() . ' c id: ' . $stock->id);

            Log::channel('documents')
                ->info('Значения count: ' . $stock->count . ', reserve: ' . $stock->reserve . ', free: ' . $stock->free);

            $reserve_count = $item->reserve->count;

            $stock->free += $reserve_count;
            $stock->reserve -= $reserve_count;
            $stock->save();

            Log::channel('documents')
                ->info('Обновлены значения count: ' . $stock->count . ', reserve: ' . $stock->reserve . ', free: ' . $stock->free);

            $result = $item->reserve->update([
                'count' => 0
            ]);

            Log::channel('documents')
                ->info('Ставим количество 0 в атуальынй резерв с id: ' . $item->reserve->id . ', результат ' . $result);


            $result = ReservesHistory::where([
                'reserve_id' => $item->reserve->id,
                'archive' => false
            ])->update([
                'archive' => true
            ]);

            Log::channel('documents')
                ->info('Ставим всей истории резерва архив, результат ' . $result);

            $item->update([
                'is_reserved' => false
            ]);

            $result = 'Резерв успешно снят';

            Log::channel('documents')
                ->info('=== КОНЕЦ ОТМЕНЫ РЕЗЕРВИРОВАНИЯ ===
                ');
        }  else {
            Log::channel('documents')
                ->info('Склада нет, негде ставить в резерв');
            $result = 'Не существует склада товара, невозможно снять с резерва';
        }

        return $result;
    }
}
