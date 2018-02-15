<?php

namespace App\Scopes\Traits;

trait SystemItemTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeSystemItem($query, $answer)
    {
        // Получаем из массива answer нужную информацию:
        // $dependence = $answer['dependence'];
        // $filials = $answer['filials'];

        $system_item = $answer['system_item'];
        $user_status = $answer['user_status'];
        $company_id = $answer['company_id'];
        $entity_name = $answer['entity_name'];


        // ЗАВИСИМОСТЬ ОТ СИСТЕМНЫХ ЗАПИСЕЙ  -----------------------------------------------------------------------------------------------------------

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
                    ->orWhere('system_item', 1)
                    ->orWhereNull('system_item')
                    ;});

                };
            };

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ПРОВЕРКА ДЛЯ ОБЫЧНЫХ ПОЛЬЗОВАТЕЛЕЙ  ---------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

            //Выключаем отображение богов на странице списка пользователей
            if($entity_name == 'users'){
                $query = $query->Where('god', null);
            };


            if(($user_status == null)&&($system_item == 1)){

                // Если есть право смотреть системные
                return $query
                ->Where(function ($query) use ($company_id) {$query
                ->Where('company_id', $company_id)  
                ->orWhere('system_item', 1)
                ->orWhereNull('system_item')
                ;});

            };



            if(($user_status == null)&&($system_item == null)){


                // Если нет права смотреть системные
                return $query
                ->Where(function ($query) use ($company_id) {$query
                ->Where('company_id', $company_id)   
                ->Where('system_item', null)
                ;});

            };

    }

}
