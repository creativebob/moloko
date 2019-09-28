<?php

namespace App\Observers;

use App\ConsignmentsItem;

use App\Entity;
use App\Observers\Traits\CommonTrait;

class ConsignmentsItemObserver
{

    use CommonTrait;

    public function creating(ConsignmentsItem $consignments_item)
    {
        $request = request();
        $entity = Entity::find($request->entity_id);
        $consignments_item->cmv_type = 'App\\'.$entity->model;

        $this->store($consignments_item);
        $this->setTotal($consignments_item);
    }

    public function updating(ConsignmentsItem $consignments_item)
    {
        $this->update($consignments_item);
        $this->setTotal($consignments_item);
    }

    public function deleting(ConsignmentsItem $consignments_item)
    {
        $this->destroy($consignments_item);
    }

    private function setTotal($consignments_item)
    {
        $consignments_item->total =  $consignments_item->count *  $consignments_item->price;
    }
}
