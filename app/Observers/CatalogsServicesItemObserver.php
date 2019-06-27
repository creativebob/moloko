<?php

namespace App\Observers;

use App\CatalogsServicesItem;

use App\Observers\Traits\CommonTrait;

class CatalogsServicesItemObserver
{
    use CommonTrait;

    public function creating(CatalogsServicesItem $catalogs_services_item)
    {
        $this->store($catalogs_services_item);
    }

    public function updating(CatalogsServicesItem $catalogs_services_item)
    {
        $this->update($catalogs_services_item);
    }

    public function deleting(CatalogsServicesItem $catalogs_services_item)
    {
        $this->destroy($catalogs_services_item);
    }

    public function created(CatalogsServicesItem $catalogs_services_item)
    {
        $request = request();
        // dd($request);
        // Cохраняем / обновляем фото
        savePhoto($request, $catalogs_services_item);
    }
}
