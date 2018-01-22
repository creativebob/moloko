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
              
                return $query;
            } else {

                return $query->Where('system_item', null);
            };

        } else {
            
          return $query->Where('system_item', null);
        };
    }

}
