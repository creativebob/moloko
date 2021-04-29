<?php

namespace App\Models\System\Flows;

use App\Service;

class ServicesFlow extends ProcessFlow
{
    const ALIAS = 'services_flows';
    const DEPENDENCE = true;

    public function process()
    {
        return $this->belongsTo(Service::class);
    }
}
