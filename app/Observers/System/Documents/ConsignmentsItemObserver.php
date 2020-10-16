<?php

namespace App\Observers\System\Documents;

use App\Models\System\Documents\ConsignmentsItem;
use App\Entity;
use App\Observers\System\BaseObserver;

class ConsignmentsItemObserver extends BaseObserver
{
    /**
     * Handle the consignments item "creating" event.
     *
     * @param ConsignmentsItem $consignmentsItem
     */
    public function creating(ConsignmentsItem $consignmentsItem)
    {
        $request = request();
        $entity = Entity::find($request->entity_id);
        $consignmentsItem->cmv_type = $entity->model;

        $this->store($consignmentsItem);
        $this->setAmount($consignmentsItem);
    }

    /**
     * Handle the consignments item "updating" event.
     *
     * @param ConsignmentsItem $consignmentsItem
     */
    public function updating(ConsignmentsItem $consignmentsItem)
    {
        $this->update($consignmentsItem);
        $this->setAmount($consignmentsItem);
    }

    /**
     * Handle the consignments item "deleting" event.
     *
     * @param ConsignmentsItem $consignmentsItem
     */
    public function deleting(ConsignmentsItem $consignmentsItem)
    {
        $this->destroy($consignmentsItem);
    }

    /**
     * Сумма по позиции
     *
     * @param $consignmentsItem
     */
    private function setAmount($consignmentsItem)
    {
        $consignmentsItem->amount =  $consignmentsItem->count * $consignmentsItem->cost;
    }
}
