<?php

namespace App\Observers;

use App\Goods;

use App\Observers\Traits\CommonTrait;

class GoodsObserver
{

    use CommonTrait;

    public function creating(Goods $cur_goods)
    {
        $this->store($cur_goods);
    }

    public function updating(Goods $cur_goods)
    {
        $this->update($cur_goods);
    }

    public function deleting(Goods $cur_goods)
    {
        $this->destroy($cur_goods);
    }
}
