<?php

namespace App\Models\System\Traits;

use App\Location;

trait Locationable
{

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

}
