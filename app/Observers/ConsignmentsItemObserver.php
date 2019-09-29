<?php

namespace App\Observers;

use App\ConsignmentsItem;

use App\Entity;
use App\Observers\Traits\Commonable;

class ConsignmentsItemObserver
{

    use Commonable;

    public function creating(ConsignmentsItem $consignments_item)
    {
        $request = request();
        $entity = Entity::find($request->entity_id);
        $consignments_item->cmv_type = 'App\\'.$entity->model;

        $this->store($consignments_item);
        $this->setAmount($consignments_item);
    }

    public function updating(ConsignmentsItem $consignments_item)
    {
        $this->update($consignments_item);
        $this->setAmount($consignments_item);
    }

    public function deleting(ConsignmentsItem $consignments_item)
    {
        $this->destroy($consignments_item);
    }

    private function setAmount($consignments_item)
    {
        $consignments_item->amount =  $consignments_item->count * $consignments_item->price;
    }
}
