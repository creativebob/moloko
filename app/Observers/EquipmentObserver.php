<?php

namespace App\Observers;

use App\Equipment;

use App\Observers\Traits\CommonTrait;

class EquipmentObserver
{
    use CommonTrait;

    public function creating(Equipment $equipment)
    {
        $this->store($equipment);
    }

    public function updating(Equipment $equipment)
    {
        $this->update($equipment);
    }

    public function deleting(Equipment $equipment)
    {
        $this->destroy($equipment);
    }
}
