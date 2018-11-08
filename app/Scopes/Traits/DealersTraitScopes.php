<?php

namespace App\Scopes\Traits;
use App\Dealer;

trait DealersTraitScopes
{
    // Фильтрация по городу
    public function scopeDealers($query, $company_id = null)
    {
        if($company_id != null){
            $dealers = Dealer::where('company_id', $company_id)->get()->keyBy('contragent_id')->keys()->toArray();
            $query = $query->whereIn('id', $dealers);
        }
        return $query;
    }
}
