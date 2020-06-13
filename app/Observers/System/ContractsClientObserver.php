<?php

namespace App\Observers\System;

use App\ContractsClient;

use App\Observers\System\Traits\Commonable;

class ContractsClientObserver
{

    use Commonable;

    public function creating(ContractsClient $contracts_client)
    {
        $this->store($contracts_client);
//        $contracts_client->date = today();

//        $answer = operator_right('contracts_clients', false, getmethod('index'));
//        $contracts_clients_count = ContractsClient::moderatorLimit($answer)
//            ->companiesLimit($answer)
//            ->systemItem($answer)
//            ->count();
//
//        $contracts_client->number = $contracts_clients_count + 1;
    
        $contracts_client->debit =  $contracts_client->amount;
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
