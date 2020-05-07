<?php

namespace App\Observers\System;

use App\ClientsLoyalitiesScore;
use App\Observers\System\Traits\Commonable;

class ClientsLoyalitiesScoreObserver
{

    use Commonable;

    public function creating(ClientsLoyalitiesScore $clientsLoyaltiesScore)
    {
        $this->store($clientsLoyaltiesScore);
    }

//    public function updating(ClientsLoyaltiesScore $clientsLoyaltiesScore)
//    {
//        $this->update($clientsLoyaltiesScore);
//    }
//
//    public function deleting(ClientsLoyaltiesScore $clientsLoyaltiesScore)
//    {
//        $this->destroy($clientsLoyaltiesScore);
//    }
}
