<?php

namespace App\Scopes\Traits;

trait SystemitemTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeSystemitem($query, $system_item, $user_status, $company_id)
    {

        // ЗАВИСИМОСТЬ ОТ СИСТЕМНЫХ ЗАПИСЕЙ  -----------------------------------------------------------------------------------------------------------
        if(isset($system_item)){

            if($user_status == 1){

                if($company_id == null){
                    return $query->WhereNull('system_item')->orWhere('system_item', 1);
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
