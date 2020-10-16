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
     * Резервирование позиции документа на складе
     *
     * @param object $item
     * @return string|null
     */
    public function reserve($item)
    {
        $entity_document = Entity::where('alias', $item->document->getTable())->first();
        $model_document = $entity_document->model;

        if ($item->document->getTable() == 'estimates') {
            $model_document_item = $model_document.'sGoodsItem';
        } else {
            $model_document_item = $model_document.'sItem';
        }

        Log::channel('documents')
            ->info('=== РЕЗЕРВИРОВАНИЕ ' . $item->getTable() . ' ' . $item->id . ' ===');

        // Списываем позицию состава
        $stock_general = Stock::find($item->stock_id);

        // Списываем позицию состава
        $product = $item->product;

        $entity_product = Entity::where('alias', $product->getTable())->first();
        $model_product = $entity_product->model;

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
                    $result = "По позиции \"{$item->product->article->name}\" резерв поставлен не на все количество, недостаточно " . ($item_count - $stock->free);

                    $item_count = $stock->free;

                    $stock->free -= $item_count;
                    $stock->reserve += $item_count;
                } else {
                    $result = null;
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
                   'is_reserved' => 1
                ]);

                Log::channel('documents')
                    ->info('=== КОНЕЦ РЕЗЕРВИРОВАНИЯ ===
                        ');
            } else {
                Log::channel('documents')
                    ->info('На сладе свободных остатков нет');
                $result = "По позиции \"{$item->product->article->name}\" на сладе свободных остатков нет";
            }
        } else {
            Log::channel('documents')
                ->info('Склада нет, негде ставить в резерв');
            $result = "По позиции \"{$item->product->article->name}\" не существует склада товара, невозможно поставить в резерв";
        }


        return $result;
    }

    /**
     * Отмена резервирования пункта
     *
     * @param $item
     * @return string|null
     */
    public function unreserve($item)
    {
        if (optional($item->reserve)->count > 0) {
            Log::channel('documents')
                ->info('=== ОТМЕНА РЕЗЕРВИРОВАНИЯ ' . $item->getTable() . ' ' . $item->id . ' ===');

            $stock_general = Stock::find($item->stock_id);

            $product = $item->product;

            // Ищем хранилище
            $storage = $product->stocks->where('stock_id', $stock_general->id)->where('filial_id', $stock_general->filial_id)->where('manufacturer_id', $product->article->manufacturer_id)->first();
            if ($storage) {

                Log::channel('documents')
                    ->info("Существует хранилище {$storage->getTable()} c id: {$storage->id}");

                Log::channel('documents')
                    ->info('Значения count: ' . $storage->count . ', reserve: ' . $storage->reserve . ', free: ' . $storage->free);

                $reserve_count = $item->reserve->count;

                $storage->free += $reserve_count;
                $storage->reserve -= $reserve_count;
                $storage->save();


                Log::channel('documents')
                    ->info('Обновлены значения count: ' . $storage->count . ', reserve: ' . $storage->reserve . ', free: ' . $storage->free);

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
                    'is_reserved' => 0
                ]);

                $result = null;

                Log::channel('documents')
                    ->info('=== КОНЕЦ ОТМЕНЫ РЕЗЕРВИРОВАНИЯ ===
                    ');
            }  else {
                Log::channel('documents')
                    ->info('Склада нет, негде ставить в резерв');
                $result = "По позиции \"{$item->product->article->name}\" не существует склада, невозможно снять с резерва";
            }
        } else {
            Log::channel('documents')
                ->info("=== ПО ПУНКТУ {$item->getTable()} {$item->id} РЕЗЕРВА НЕТ ===
                    ");
            $result = null;
        }

        return $result;
    }
}
