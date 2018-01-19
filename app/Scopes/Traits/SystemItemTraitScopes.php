<?php

namespace App\Scopes\Traits;

trait SystemitemTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeSystemitem($query, $system_item)
    {


        // ЗАВИСИМОСТЬ ОТ СИСТЕМНЫХ ЗАПИСЕЙ  -----------------------------------------------------------------------------------------------------------

        if(isset($system_item)){
            if($system_item == 1){

                return $query->orWhere('system_item', 1);
            } else {
                return $query;
            };

        } else {
            
          return $query;
        };
    }

}
