<?php

namespace App\Models\System\Flows;

use App\Client;
use App\Service;
use App\Staffer;

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

    public function staff()
    {
        return $this->belongsToMany(Staffer::class, 'services_flow_staffer');
    }
}
