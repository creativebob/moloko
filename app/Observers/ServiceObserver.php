<?php

namespace App\Observers;

use App\Service;

use App\Observers\Traits\CommonTrait;

class ServiceObserver
{
    use CommonTrait;

    public function creating(Service $service)
    {
        $this->store($service);
    }

    public function updating(Service $service)
    {
        $this->update($service);
    }

    public function deleting(Service $service)
    {
        $this->destroy($service);
    }
}