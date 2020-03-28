<?php

namespace App\Observers\System;

use App\Container;

use App\Observers\System\Traits\Commonable;

class ContainerObserver
{

    use Commonable;

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
