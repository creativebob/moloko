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

                    // return $query
                    // ->Where(function ($query){$query
                    // ->Where('system_item', 1)
                    // // ->WhereNotNull('company_id')
                    // // ->WhereNull('company_id')
                    // ->orWhereNull('system_item');});

                };
            };

            if(($user_status == null)&&($system_item == 1)){

                return $query->orWhere('system_item', 1);
            };

        } else {
            
          return $query->WhereNull('system_item');
        };
    }

}
