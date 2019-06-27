<?php

namespace App\Observers;

use App\EquipmentsCategory;

use App\Observers\Traits\CommonTrait;

class EquipmentsCategoryObserver
{

    use CommonTrait;

    public function creating(EquipmentsCategory $equipments_category)
    {
        $this->store($equipments_category);
    }

    public function updating(EquipmentsCategory $equipments_category)
    {
        $this->update($equipments_category);
    }

    public function deleting(EquipmentsCategory $equipments_category)
    {
        $this->destroy($equipments_category);
    }
}
