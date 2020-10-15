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
        if ($stock->count < 0) {
            $stock->free = 0;
        } else {
            // TODO - 14.10.2020 - Нужна логика по расчету свободных остатков в зависимости от резервов
            $stock->free = $stock->count;
        }
    }
}
