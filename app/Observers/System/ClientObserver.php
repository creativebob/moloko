<?php

namespace App\Observers\System;

use App\Client;

use App\Observers\System\Traits\Commonable;

class ClientObserver
{

    use Commonable;

    public function creating(Client $client)
    {
        // TODO - 04.03.20 - display false для клиентов, пока закооментил методы, раскомментить при рефакторе контроллера
        $client->display = false;
        $this->store($client);
    }

    public function updating(Client $client)
    {
//        $this->update($client);
    }

    public function deleting(Client $client)
    {
//        $this->destroy($client);
    }
}
