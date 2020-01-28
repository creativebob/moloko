<?php

namespace App\Observers;

use App\EstimatesServicesItem;

use App\Observers\Traits\Commonable;

class EstimatesServicesItemObserver
{

    use Commonable;

    public function creating(EstimatesServicesItem $estimates_services_item)
    {
        $this->store($estimates_services_item);
    }

    public function updating(EstimatesServicesItem $estimates_services_item)
    {
        $this->update($estimates_services_item);
        $estimates_services_item->amount = $estimates_services_item->count * $estimates_services_item->price;
    }

    public function deleting(EstimatesServicesItem $estimates_services_item)
    {
        $this->destroy($estimates_services_item);
    }
}
