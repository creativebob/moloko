<?php

namespace App\Observers\System;

use App\Observers\System\Traits\Commonable;
use App\CatalogsService;

class CatalogsServiceObserver
{

    use Commonable;

    public function creating(CatalogsService $catalogs_service)
    {
        $this->store($catalogs_service);
    }

    public function updating(CatalogsService $catalogs_service)
    {
        $this->update($catalogs_service);
    }

    public function deleting(CatalogsService $catalogs_service)
    {
        $this->destroy($catalogs_service);
    }

    public function saving(CatalogsService $catalogs_service)
    {
        $this->setSlug($catalogs_service);
    }

}
