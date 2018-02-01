<?php

namespace App\Scopes\Traits;

trait FilialsTraitScopes
{
    // Фильтрация по филиалу
    public function scopeFilials($query, $filials, $dependence)
    {

      // ПОКАЗ СПИСКА ПОЛЬЗОВАТЕЛЕЙ ---------------------------------------------------------------------------------------------------------------
      if($filials != null){

        if($dependence){
            return $query->whereIn('filial_id', $filials);
        } else {
          return $query;
        };

      } else {
          return $query;
      }

    }

}
