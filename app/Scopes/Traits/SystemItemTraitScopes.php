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

        $system = $answer['system']['result'];
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
                        return $query->where('id', 1);
                    } else {
                        return $query->where('system', false)->orWhere('system', true);
                    };


                } else
                {

                    return $query
                    ->Where(function ($query) {$query
                    ->Where('company_id', null)            
                    ->orWhere('system', true)
                    ->orWhere('system', false)
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


            if(($user_status == null)&&($system == true)){



                // Если есть право смотреть системные
                return $query
                ->Where(function ($query) use ($company_id) {$query
                ->Where('company_id', $company_id)  
                ->orWhere('system', true)
                ->orWhere('system', false)
                ;});

            };



            if(($user_status == null)&&($system == false)){


                // Если нет права смотреть системные
                return $query
                ->Where(function ($query) use ($company_id) {$query
                ->Where('company_id', $company_id)   
                ->where('system', false)
                ;});

            };

    }

}
