<?php

namespace App\Models\System\Flows;

use App\Client;
use App\Service;

class ServicesFlow extends ProcessFlow
{
    const ALIAS = 'services_flows';
    const DEPENDENCE = true;

    public function process()
    {
        return $this->belongsTo(Service::class);
    }

    public function events()
    {
        return $this->hasMany(EventsFlow::class, 'initiator_id');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class);
    }
}
