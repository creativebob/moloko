<?php

namespace App\Observers;

use App\Observers\Traits\CategoriesTrait;
use App\Observers\Traits\CommonTrait;
use App\EquipmentsCategory;

class EquipmentsCategoryObserver
{

    use CommonTrait;
    use CategoriesTrait;

    public function creating(EquipmentsCategory $equipments_category)
    {
        $this->store($equipments_category);
        $this->storeCategory($equipments_category);
    }

    public function updating(EquipmentsCategory $equipments_category)
    {
        $this->update($equipments_category);
        $this->updateCategory($equipments_category);
    }

    public function updated(EquipmentsCategory $equipments_category)
    {
        $this->updateCategoryChildsSlug($equipments_category);
        $this->updateCategoryChildsLevel($equipments_category);
        $this->updateCategoryChildsCategoryId($equipments_category);
    }

    public function deleting(EquipmentsCategory $equipments_category)
    {
        $this->destroy($equipments_category);
    }
}
