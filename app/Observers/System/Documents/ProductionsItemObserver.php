<?php

namespace App\Observers\System\Documents;

use App\Observers\System\BaseObserver;
use App\Models\System\Documents\ProductionsItem;
use App\Entity;

class ProductionsItemObserver extends BaseObserver
{

    /**
     * Handle the productions item "creating" event.
     *
     * @param ProductionsItem $productionsItem
     */
    public function creating(ProductionsItem $productionsItem)
    {
        $request = request();
        $entity = Entity::find($request->entity_id);
        $productionsItem->cmv_type = $entity->model;

        $this->store($productionsItem);
    }

    /**
     * Handle the productions item "updating" event.
     *
     * @param ProductionsItem $productionsItem
     */
    public function updating(ProductionsItem $productionsItem)
    {
        $this->update($productionsItem);
    }

    /**
     * Handle the productions item "deleting" event.
     *
     * @param ProductionsItem $productionsItem
     */
    public function deleting(ProductionsItem $productionsItem)
    {
        $this->destroy($productionsItem);
    }
}
