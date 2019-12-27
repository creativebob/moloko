<?php

namespace App\Observers;

use App\Observers\Traits\Commonable;
use App\CatalogsGoods;

class CatalogsGoodsObserver
{

    use Commonable;

    public function creating(CatalogsGoods $catalogs_goods)
    {
        $this->store($catalogs_goods);
    }

    public function updating(CatalogsGoods $catalogs_goods)
    {
        $this->update($catalogs_goods);
    }

    public function deleting(CatalogsGoods $catalogs_goods)
    {
        $this->destroy($catalogs_goods);
    }

    public function saving(CatalogsGoods $catalogs_goods)
    {
        $this->setSlug($catalogs_goods);
    }

}
