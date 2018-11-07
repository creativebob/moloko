<?php

namespace App\Scopes\Traits;
use App\Client;

trait ClientsTraitScopes
{
    // Фильтрация по городу
    public function scopeClients($query, $company_id = null)
    {
        if($company_id != null){
            $clients = Client::where('company_id', $company_id)->get()->keyBy('contragent_id')->keys()->toArray();
            $query = $query->whereIn('id', $clients);
        }
        return $query;
    }
}
