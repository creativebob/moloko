<?php

namespace App\Scopes\Traits;

trait SystemitemTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeSystemitem($query, $answer)
    {

        // Получаем из массива answer нужную информацию:
        // $dependence = $answer['dependence'];
        // $filials = $answer['filials'];

        $system_item = $answer['system_item'];
        $user_status = $answer['user_status'];
        $company_id = $answer['company_id'];
        $entity_name = $answer['entity_name'];

        // ЗАВИСИМОСТЬ ОТ СИСТЕМНЫХ ЗАПИСЕЙ  -----------------------------------------------------------------------------------------------------------
        if(isset($system_item)){

            if($user_status == 1){

                if($company_id == null){

                    // Если бог смотрит на список сайтов, то показываем только системные сайты: CRM System
                    if($entity_name == 'sites'){
                        return $query->Where('id', 1);
                    } else {
                        return $query->WhereNull('system_item')->orWhere('system_item', 1);
                    };

                } else
                {
                    return $query->orWhere('system_item', 1); // Рабочая версия

                };
            };

            if(($user_status == null)&&($system_item == 1)){

                // $system_filials = collect(getLS('users', 'system', 'filials'))->keys()->toarray(); 

                return $query
                ->orWhere(function ($query) {$query
                ->Where('company_id', null)            
                ->Where('system_item', 1)
                ;});
            };

        } else {
            
            return $query->WhereNull('system_item');
        };
    }

}
