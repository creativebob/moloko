<?php

namespace App\Observers\System;

use App\Client;
use App\Observers\System\Traits\Commonable;

class ClientObserver
{

    use Commonable;

    /**
     * Handle the client "creating" event.
     *
     * @param Client $client
     */
    public function creating(Client $client)
    {
        $this->store($client);

        $client->display = false;

        $client->filial_id = auth()->user()->stafferFilialId;
    }

    /**
     * Handle the client "updating" event.
     *
     * @param Client $client
     */
    public function updating(Client $client)
    {
        $this->update($client);
    }
}
