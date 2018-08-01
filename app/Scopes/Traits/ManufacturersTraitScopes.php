<?php

namespace App\Scopes\Traits;
use App\Manufacturer;

trait ManufacturersTraitScopes
{
    // Фильтрация по городу
    public function scopeManufacturers($query, $company_id = null)
    {
        if($company_id != null){
            $manufacturers = Manufacturer::where('company_id', $company_id)->get()->keyBy('contragent_id')->keys()->toArray();
            $query = $query->whereIn('id', $manufacturers);
        }
        return $query;
    }
}
