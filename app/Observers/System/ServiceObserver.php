<?php

namespace App\Observers\System;

use App\Service;

class ServiceObserver extends BaseObserver
{

    /**
     * Handle the service "creating" event.
     *
     * @param Service $service
     */
    public function creating(Service $service)
    {
        $this->store($service);
    }

    /**
     * Handle the service "updating" event.
     *
     * @param Service $service
     */
    public function updating(Service $service)
    {
        $this->update($service);
    }

    /**
     * Handle the service "deleting" event.
     *
     * @param Service $service
     */
    public function deleting(Service $service)
    {
        $this->destroy($service);
    }
}
