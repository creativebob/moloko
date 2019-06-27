<?php

namespace App\Observers;

use App\Plugin;

use App\Observers\Traits\CommonTrait;

class PluginObserver
{
    use CommonTrait;

    public function creating(Plugin $plugin)
    {
        $this->store($plugin);
    }

    public function updating(Plugin $plugin)
    {
        $this->update($plugin);
    }

    public function deleting(Plugin $plugin)
    {
        $this->destroy($plugin);
    }
}
