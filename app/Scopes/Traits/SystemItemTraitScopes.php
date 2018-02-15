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

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ПРОВЕРКА ДЛЯ БОГОВ --------------------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

            if($user_status == 1){

                if($company_id == null){

                    // Если бог смотрит на список сайтов, то показываем только системные сайт CRM System
                    if($entity_name == 'sites'){
                        return $query->Where('id', 1);
                    } else {
                        return $query->WhereNull('system_item')->orWhere('system_item', 1);
                    };

                } else
                {

                    return $query
                    ->Where(function ($query) {$query
                    ->Where('company_id', null)            
                    ->Where('system_item', 1)
                    ->orWhere('system_item', null)
                    ;});

                };
            };

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ПРОВЕРКА ДЛЯ ОБЫЧНЫХ ПОЛЬЗОВАТЕЛЕЙ  ---------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

            if(($user_status == null)&&($system_item == 1)){

                // Если есть право смотреть системные
                return $query
                ->Where(function ($query) use ($company_id) {$query
                ->Where('company_id', $company_id)            
                ->orWhere('system_item', 1)
                ->orWhere('system_item', null)
                ;});

            };

            if(($user_status == null)&&($system_item == null)){

                dd(324535);
                // Если нет права смотреть системные
                return $query
                ->Where(function ($query) use ($company_id) {$query
                ->Where('company_id', $company_id)            
                ->Where('system_item', null)
                ;});
            };

        } else {
            
            return $query->WhereNull('system_item');
        };
    }

}
