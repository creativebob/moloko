<?php

namespace App\Observers;

use App\ProductionsItem;

use App\Entity;
use App\Observers\Traits\Commonable;

class ProductionsItemObserver
{

    use Commonable;

    public function creating(ProductionsItem $productions_item)
    {
        $request = request();
        $entity = Entity::find($request->entity_id);
        $productions_item->cmv_type = 'App\\'.$entity->model;

        $this->store($productions_item);
    }

    public function updating(ProductionsItem $productions_item)
    {
        $this->update($productions_item);
    }

    public function deleting(ProductionsItem $productions_item)
    {
        $this->destroy($productions_item);
    }
}
