<?php

namespace App\Scopes\Traits;

trait CompaniesLimitTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeCompaniesLimit($query, $answer)
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
