<?php

namespace App\Observers;

use App\Observers\Traits\CommonTrait;
use App\CatalogsGoods;

class CatalogsGoodsObserver
{

    use CommonTrait;

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

    public function saved(CatalogsGoods $catalogs_goods)
    {
        $this->syncSites($catalogs_goods);
    }

    protected function syncSites(CatalogsGoods $catalogs_goods)
    {
        $request = request();
        $catalogs_goods->sites()->sync($request->sites);
    }
}
