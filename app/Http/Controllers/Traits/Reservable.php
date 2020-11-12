<?php

namespace App\Http\Controllers\Traits;

use App\Entity;
use App\Reserve;
use App\ReservesHistory;
use App\Stock;

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
        $modelDocument = Entity::where('alias', $item->document->getTable())
            ->value('model');

        $modelDocumentItem = Entity::where('alias', $item->getTable())
        ->value('model');

        logs('documents')
            ->info('=== РЕЗЕРВИРОВАНИЕ ' . $item->getTable() . ' ' . $item->id . ' ===');

        // Списываем позицию состава
        $stockGeneral = Stock::find($item->stock_id);

        // Списываем позицию состава
        $cmv = $item->product;

        $modelCmv = Entity::where('alias', $cmv->getTable())
            ->value('model');

//      dd($stock_goods);

        $storage = $cmv->stocks->where('stock_id', $stockGeneral->id)
            ->where('filial_id', $stockGeneral->filial_id)
            ->where('manufacturer_id', $cmv->article->manufacturer_id)
            ->first();
        if ($storage) {

            logs('documents')
                ->info('Существует склад ' . $storage->getTable() . ' c id: ' . $storage->id);

            logs('documents')
                ->info('Значения count: ' . $storage->count . ', reserve: ' . $storage->reserve . ', free: ' . $storage->free);

            $itemCount = $item->count;

            if ($storage->free > 0) {

                if ($itemCount > $storage->free) {
                    $result = "По позиции \"{$item->product->article->name}\" резерв поставлен не на все количество, недостаточно " . ($itemCount - $storage->free);

                    $itemCount = $storage->free;
                    $storage->reserve += $itemCount;
                    $storage->free -= $itemCount;
                } else {
                    $result = null;
                    $storage->reserve += $itemCount;
                    $storage->free -= $itemCount;
                }

                logs('documents')
                    ->info('Существует склад ' . $storage->getTable() . ' c id: ' . $storage->id);

                $storage->save();

                logs('documents')
                    ->info('Обновлены значения count: ' . $storage->count . ', reserve: ' . $storage->reserve . ', free: ' . $storage->free);

                if ($item->reserve) {
                    $reserve = $item->reserve;
                    $reserve->count += $itemCount;
                    $reserve->save();

                    $reserve->history()->save(
                        ReservesHistory::make([
                            'count' => $itemCount
                        ])
                    );

                    logs('documents')
                        ->info('Обновили актуальный резерв с id: ' . $reserve->id .  ', count: ' . $reserve->count);

                } else {

                    $modelStorage = Entity::where('alias', $cmv->getTable() . '_stocks')
                        ->value('model');

                    $reserve = Reserve::create([
                        'document_id' => $item->document->id,
                        'document_type' => $modelDocument,

                        'documents_item_id' => $item->id,
                        'documents_item_type' => $modelDocumentItem,

                        'cmv_id' => $cmv->id,
                        'cmv_type' => $modelCmv,

                        'storage_id' => $storage->id,
                        'storage_type' => $modelStorage,

                        'count' => $itemCount,
                        'stock_id' => $item->stock_id,
                        'filial_id' => $item->document->filial_id,
                    ]);

                    logs('documents')
                        ->info('Записали актуальный резерв с id: ' . $reserve->id .  ', count: ' . $reserve->count);
                }

                $item->update([
                   'is_reserved' => 1
                ]);

                logs('documents')
                    ->info('=== КОНЕЦ РЕЗЕРВИРОВАНИЯ ===
                        ');
            } else {
                logs('documents')
                    ->info('На сладе свободных остатков нет');
                $result = "По позиции \"{$item->product->article->name}\" на складе свободных остатков нет";
            }
        } else {
            logs('documents')
                ->info('Склада нет, негде ставить в резерв');
            $result = "По позиции \"{$item->product->article->name}\" не существует склада, невозможно поставить в резерв";
        }

        return $result;
    }

    /**
     * Отмена резервирования пункта
     *
     * @param $item
     * @return string|null
     */
    public function cancelReserve($item)
    {
        if (optional($item->reserve)->count > 0) {
            logs('documents')
                ->info('=== ОТМЕНА РЕЗЕРВИРОВАНИЯ ' . $item->getTable() . ' ' . $item->id . ' ===');

            // Ищем хранилище
            $storage = $item->reserve->storage;
            if ($storage) {

                logs('documents')
                    ->info("Существует хранилище {$storage->getTable()} c id: {$storage->id}");

                logs('documents')
                    ->info('Значения count: ' . $storage->count . ', reserve: ' . $storage->reserve . ', free: ' . $storage->free);

                $reserveCount = $item->reserve->count;

                $storage->reserve -= $reserveCount;
                $storage->free += $reserveCount;

                $storage->save();


                logs('documents')
                    ->info('Обновлены значения count: ' . $storage->count . ', reserve: ' . $storage->reserve . ', free: ' . $storage->free);

                $result = $item->reserve->update([
                    'count' => 0
                ]);

                logs('documents')
                    ->info('Ставим количество 0 в атуальынй резерв с id: ' . $item->reserve->id . ', результат ' . $result);

                $result = ReservesHistory::where([
                    'reserve_id' => $item->reserve->id,
                    'archive' => false
                ])
                    ->update([
                    'archive' => true
                ]);

                logs('documents')
                    ->info('Ставим всей истории резерва архив, результат ' . $result);

                $item->update([
                    'is_reserved' => 0
                ]);

                $result = null;

                logs('documents')
                    ->info('=== КОНЕЦ ОТМЕНЫ РЕЗЕРВИРОВАНИЯ ===
                    ');
            }  else {
                logs('documents')
                    ->info('Склада нет, негде снимать с резерва');
                $result = "По позиции \"{$item->product->article->name}\" не существует склада, невозможно снять с резерва";
            }
        } else {
            logs('documents')
                ->info("=== ПО ПУНКТУ {$item->getTable()} {$item->id} РЕЗЕРВА НЕТ ===
                    ");
            $result = null;
        }

        return $result;
    }
}
