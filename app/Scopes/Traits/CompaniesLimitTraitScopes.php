<?php

namespace App\Scopes\Traits;

trait CompaniesLimitTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeCompaniesLimit($query, $answer)
    {

        // Получаем из массива answer нужную информацию:
        $company_id = $answer['company_id'];
        $entity_name = $answer['entity_name'];

        // ФИЛЬТРАЦИЯ ПО КОМПАНИИ  -----------------------------------------------------------------------------------------------------------

        if($company_id == null){

            // Показываем записи всех компаний
            return $query;

        } else {

            // Показываем записи выбранной компании
            // return $query->Where('company_id', $companies)->orWhere('id', $companies);
            return $query->where('company_id', $company_id)
            ->when($entity_name == 'companies', function($q) use ($company_id){
                $q->orWhere('id', $company_id);
            });
        };
    }

}
