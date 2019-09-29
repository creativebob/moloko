<?php

namespace App\Observers;

use App\Plugin;

use App\Observers\Traits\Commonable;

class PluginObserver
{
    use Commonable;

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
