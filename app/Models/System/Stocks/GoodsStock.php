<?php

namespace App\Models\System\Stocks;

use App\Goods;

class GoodsStock extends CmvStock
{

    const ALIAS = 'goods_stocks';
    const DEPENDENCE = true;

    public function cmv()
    {
        return $this->belongsTo(Goods::class);
    }

}
