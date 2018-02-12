<?php

namespace App\Scopes\Traits;

trait CompaniesFilterTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeCompaniesFilter($query, $answer)
    {

        // Получаем из массива answer нужную информацию:
        $companies = $answer['company_id'];

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
