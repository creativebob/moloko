<?php

namespace App\Observers\System\Stocks;

use App\Observers\System\BaseObserver;

class CmvStockObserver extends BaseObserver
{

    /**
     * Проверка свободных остатков (не меньше 0)
     *
     * @param $stock
     */
    public function setFree($stock)
    {

        // Приходуем / списываем
//        if ($stock->isDirty('count')) {
//
//        }
//
//        // Ставим / снимаем резерв
//        if ($stock->isDirty('reserve')) {
//
//        }

    }
}
