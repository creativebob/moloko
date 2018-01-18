<?php

namespace App\Scopes\Traits;

trait SystemItemTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeSystemItem($query, $session)
    {


        // ЗАВИСИМОСТЬ ОТ СИСТЕМНЫХ ЗАПИСЕЙ  -----------------------------------------------------------------------------------------------------------
        // Проверяем право просмотра системных записей:
        
        if(isset($session['all_rights']['system-users-allow']) && (!isset($session['all_rights']['system-users-deny'])))
        {
            $system_item = 1;
        } else {
            $system_item = null;
        };

        if(isset($system_item)){

          return $query->orWhere('system_item', 1);

        } else {

          return $query;
        };
    }

}
