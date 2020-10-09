<?php

namespace App\Models\System\Traits;

use App\Client;

trait Clientable
{

    public function client()
    {
        return $this->morphOne(Client::class, 'clientable')
            ->where([
                'archive' => false,
                'company_id' => auth()->user()->company_id
            ]);
    }

}
