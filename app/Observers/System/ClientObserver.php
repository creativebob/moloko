<?php

namespace App\Observers\System;

use App\Client;
use App\Notifications\System\Telegram;
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

        if ($client->isDirty('discount')) {
            $user = auth()->user();
            $phone = decorPhone($client->clientable->main_phone->phone);

            $msg = "ИЗМНЕНИЕ СКИДКИ\r\n";
            $msg .= "Сотрудник: {$user->name}\r\n";
            $msg .= "Клиент: {$client->clientable->name}\r\n";
            $msg .= "Филиал: {$client->filial->name}\r\n";
            $msg .= "Телефон: {$phone}\r\n";
            $msg .= "Старая скидка: {$client->getOriginal('discount')}%\r\n";
            $msg .= "Новая скидка: {$client->discount}%\r\n";

            Telegram::send(7, $msg);
        }
    }
}
