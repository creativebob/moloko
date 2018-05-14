<?php

namespace App\Scopes\Traits;
use App\Contragent;

trait ContragentsTraitScopes
{
    // Фильтрация по городу
    public function scopeContragents($query, $company_id)
    {
        if($company_id != null){
            $contragents = Contragent::where('company_id', $company_id)->get()->keyBy('contragent_id')->keys()->toArray();
            $query = $query->whereIn('id', $contragents);
        }
        return $query;
    }
}
