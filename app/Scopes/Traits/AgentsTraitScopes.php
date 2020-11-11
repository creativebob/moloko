<?php

namespace App\Scopes\Traits;
use App\Agent;

trait AgentsTraitScopes
{
    // Фильтрация по городу
    public function scopeAgents($query, $company_id = null)
    {
        if($company_id != null){
            $agents = Agent::where('company_id', $company_id)->get()->keyBy('contragent_id')->keys()->toArray();
            $query = $query->whereIn('id', $agents);
        }
        return $query;
    }
}
