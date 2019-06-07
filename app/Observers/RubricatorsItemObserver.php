<?php

namespace App\Observers;

use App\RubricatorsItem;

use App\Observers\Traits\CommonTrait;

class RubricatorsItemObserver
{
    use CommonTrait;

    public function creating(RubricatorsItem $rubricators_item)
    {
        // $this->store($rubricators_item);
    }

    public function updating(RubricatorsItem $rubricators_item)
    {
        // $this->update($rubricators_item);
    }

    public function deleting(RubricatorsItem $rubricators_item)
    {
        $this->destroy($rubricators_item);
    }
}
