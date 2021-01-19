<?php

namespace App\Http\Controllers\System\Traits;

use App\Entity;

trait Cancelable
{

    /**
     * Отмена приходования пункта
     *
     * @param $item
     */
    public function cancelReceipt($item)
    {
        logs('documents')
            ->info('=== ОТМЕНЯЕМ ПРИХОДОВАНИЕ ПУНКТА ' . $item->getTable() . ' ' . $item->id . ' ===');

        // хранилище
        $storage = $item->receipt->storage;
//        dd($storage);

        logs('documents')
            ->info('Существует хранилище ' . $storage->getTable() . ' c id: ' . $storage->id);

        $storageCount = $storage->count;

        logs('documents')
            ->info("Значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

        $dataStorage = [
            'count' => $storage->count - $item->count,
            'free' => $storage->free - $item->count,
            'weight' => ($item->cmv->article->weight * $item->count),
            'volume' => ($item->cmv->article->volume * $item->count),
        ];
        $storage->update($dataStorage);

        logs('documents')
            ->info("Обновлены значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

        // Себестоимость
        $cost = $item->cmv->cost;

        logs('documents')
            ->info('Существует себестоимость c id: ' . $cost->id);
        logs('documents')
            ->info("Значения min: {$cost->min}, max: {$cost->max} , average: {$cost->average}");

        // Получаем из сессии необходимые данные
        $answer = operator_right('consignments_items', true, 'index');

        $modelItem = Entity::where('alias', $item->getTable())->value('model');

        $min = $modelItem::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->where([
                'cmv_id' => $item->cmv_id,
                'cmv_type' => $item->cmv_type,
            ])
            ->whereHas('document', function ($q) use ($item) {
                $q->where('id', '!=', $item->document->id)
                    ->whereNotNull('conducted_at');
            })
            ->min('cost');
//					dd($min);

        $max = $modelItem::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->where([
                'cmv_id' => $item->cmv_id,
                'cmv_type' => $item->cmv_type,
            ])
            ->whereHas('document', function ($q) use ($item) {
                $q->where('id', '!=', $item->document->id)

                    ->when($item->document->getTable() == 'consignments', function ($q) {
                        $q->whereNotNull('conducted_at');
                    })
                    ->when($item->document->getTable() == 'productions', function ($q) {
                        $q->whereNotNull('conducted_at');
                    });
            })
            ->max('cost');
//					dd($max);

        if (is_null($min) || is_null($max)) {
            $average = 0;
            $min = 0;
            $max = 0;
        } else {
            $average = (($storageCount * $cost->average) - ($item->count * $item->cost)) / $storage->count;
        }
//        dd($average);
        $cost->update([
            'min' => $min,
            'max' => $max,
            'average' => $average,
        ]);
//					dd($cost);

        logs('documents')
            ->info("Обновлены значения min: {$cost->min}, max: {$cost->max} , average: {$cost->average}");

        $item->receipt()->forceDelete();

        logs('documents')
            ->info("Удалено поступление (receipt) с id: {$item->receipt->id}");

        logs('documents')
            ->info('=== КОНЕЦ ОТМЕНЫ ПРИХОДОВАНИЯ ПУНКТА ===
                        ');

    }

    /**
     * Отмена списаний пункта
     *
     * @param $item
     */
    public function cancelOffs($item)
    {

        logs('documents')
            ->info('=== ОТМЕНЯЕМ СПИСАНИЯ ===
                        ');

        foreach ($item->offs as $off) {

        $cmv = $off->cmv;

        logs('documents')
            ->info('=== ОТМЕНА СПИСАНИЯ ' . $cmv->getTable() . ' ' . $cmv->id . ' ===');

        // хранилище
        $storage = $off->storage;

        logs('documents')
            ->info('Существует хранилище ' . $storage->getTable() . ' c id: ' . $storage->id);

        logs('documents')
            ->info("Значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

        $storageCount = $storage->count;

        $data = [
            'count' => $storage->count + $off->count,
            'free' => $storage->free + $off->count,
            'weight' => $storage->weight += ($cmv->article->weight * $off->count),
            'volume' => $storage->volume += ($cmv->article->volume * $off->count),
        ];
        $storage->update($data);

        logs('documents')
            ->info("Обновлены значения count: {$storage->count}, reserve: {$storage->reserve}, free: {$storage->free}, weight: {$storage->weight}, volume: {$storage->volume}");

        // Себестоимость
        $cost = $cmv->cost;

        if ($cost) {
            logs('documents')
                ->info('Существует себестоимость c id: ' . $cost->id);
            logs('documents')
                ->info("Значения min: {$cost->min}, max: {$cost->max} , average: {$cost->average}");

            $costAverage = $cost->average;
            if ($storage->count > 0) {
                $average = (($storageCount * $costAverage) + ($off->count * $off->average)) / $storage->count;
            } else {
                $average = (($storageCount * $costAverage) + ($off->count * $off->average));
            };
            $cost->update([
                'average' => $average
            ]);

            logs('documents')
                ->info("Обновлены значения min: {$cost->min}, max: {$cost->max} , average: {$cost->average}");
        } else {
            logs('documents')
                ->info('Не существует себестоимость');
        }



        $off->forceDelete();

        logs('documents')
            ->info('Удалено списание (off) с id:' . $off->id);

        logs('documents')
            ->info('=== КОНЕЦ ОТМЕНЫ СПИСАНИЯ ===
                            ');
        }

        logs('documents')
            ->info('=== КОНЕЦ ОТМЕНЫ СПИСАНИЙ ===
                        ');

    }
}
