<?php

namespace App\Observers;

use App\Staffer;

use App\Observers\Traits\CommonTrait;

class StafferObserver
{

    use CommonTrait;

    public function creating(Staffer $staffer)
    {
        $this->store($staffer);
    }

//    public function updating(Staffer $staffer)
//    {
//        $this->update($staffer);
//    }
//
//    public function deleting(Staffer $staffer)
//    {
//        $this->destroy($staffer);
//    }
}
