<?php

namespace App\Scopes\Traits;
use App\Supplier;

trait SuppliersTraitScopes
{
    // Фильтрация по городу
    public function scopeSuppliers($query, $company_id = null)
    {
        if($company_id != null){
            $suppliers = Supplier::where('company_id', $company_id)->get()->keyBy('contragent_id')->keys()->toArray();
            $query = $query->whereIn('id', $suppliers);
        }
        return $query;
    }
}
