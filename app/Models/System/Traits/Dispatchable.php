<?php

namespace App\Models\System\Traits;

use App\Dispatch;

trait Dispatchable {

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }

    public function sendedDispatches()
    {
        return $this->hasMany(Dispatch::class)
            ->sended();
    }

    public function waitingDispatches()
    {
        return $this->hasMany(Dispatch::class)
            ->waiting();
    }
}
