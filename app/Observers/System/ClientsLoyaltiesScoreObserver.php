<?php

namespace App\Observers\System;

use App\ClientsLoyaltiesScore;
use App\Observers\System\Traits\Commonable;

class ClientsLoyaltiesScoreObserver
{

    use Commonable;

    public function creating(ClientsLoyaltiesScore $clientsLoyaltiesScore)
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
