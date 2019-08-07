<?php

namespace App\Observers;

use App\Container;

use App\Observers\Traits\CommonTrait;

class ContainerObserver
{

    use CommonTrait;

    public function creating(Container $container)
    {
        $this->store($container);
    }

    public function updating(Container $container)
    {
        $this->update($container);
    }

    public function deleting(Container $container)
    {
        $this->destroy($container);
    }
}
