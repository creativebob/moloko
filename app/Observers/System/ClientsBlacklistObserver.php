<?php

namespace App\Observers\System;

use App\ClientsBlacklist;
use App\Observers\System\Traits\Commonable;

class ClientsBlacklistObserver
{

    use Commonable;

    public function creating(ClientsBlacklist $clientsBlacklist)
    {
        $this->store($clientsBlacklist);
        $clientsBlacklist->begin_date = today();
    }

    public function updating(ClientsBlacklist $clientsBlacklist)
    {
        $this->update($clientsBlacklist);
    }

//    public function deleting(ClientsBlacklist $clientsBlacklist)
//    {
//        $this->destroy($clientsBlacklist);
//    }
}
