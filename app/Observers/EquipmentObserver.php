<?php

namespace App\Observers;

use App\Equipment;

use App\Observers\Traits\Commonable;

class EquipmentObserver
{
    use Commonable;

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
