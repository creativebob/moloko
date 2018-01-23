<?php

namespace App\Scopes\Traits;

trait CompaniesTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeCompanies($query, $companies)
    {
        // ФИЛЬТРАЦИЯ ПО КОМПАНИИ  -----------------------------------------------------------------------------------------------------------

        if($companies == null){

            // Показываем записи всех компаний
            return $query;
        } else {

            // Показываем записи выбранной компании
            return $query->Where('company_id', $companies);

        };
    }

}
