<?php

namespace App\Observers;

use App\CatalogsGoodsItem;

use App\Observers\Traits\CommonTrait;

class CatalogsGoodsItemObserver
{
    use CommonTrait;

    public function creating(CatalogsGoodsItem $catalogs_goods_item)
    {
        $this->store($catalogs_goods_item);
    }

    public function updating(CatalogsGoodsItem $catalogs_goods_item)
    {
        $this->update($catalogs_goods_item);
        $catalogs_goods_item->photo_id = savePhoto($request, $catalogs_goods_item);
    }

    public function deleting(CatalogsGoodsItem $catalogs_goods_item)
    {
        $this->destroy($catalogs_goods_item);
    }

}
