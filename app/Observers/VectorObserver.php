<?php

namespace App\Observers;

use App\Vector;

use App\Observers\Traits\Commonable;

class VectorObserver
{

    use Commonable;

    public function creating(Vector $vector)
    {
        $this->store($vector);
    }

    public function updating(Vector $vector)
    {
        $this->update($vector);
    }

    public function deleting(Vector $vector)
    {
        $this->destroy($vector);
    }
}
