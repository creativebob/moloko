<?php

namespace App\Observers;

use App\ContractsClient;

use App\Observers\Traits\Commonable;

class ContractsClientObserver
{

    use Commonable;

    public function creating(ContractsClient $contracts_client)
    {
        $this->store($contracts_client);
    }

    public function updating(ContractsClient $contracts_client)
    {
        $this->update($contracts_client);
    }

    public function deleting(ContractsClient $contracts_client)
    {
        $this->destroy($contracts_client);
    }
}
