<?php

namespace App\Models\System\Traits;

use App\Client;

trait Clientable
{

    // Получаем клиентов-компании
    // TODO - 09.10.2020 - Временный параметр, пока не получилось развести модели изи за записи в связующую телефонов
    public function client($site = null)
    {
        return $this->morphOne(Client::class, 'clientable')
            ->where([
                'archive' => false,
                'company_id' => isset($site) ? $site->company_id : optional(auth()->user())->company_id
            ]);
    }

}
