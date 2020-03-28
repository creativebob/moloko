<?php

namespace App\Observers\System;

use App\Vector;

use App\Observers\System\Traits\Commonable;

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
