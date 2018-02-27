<?php

namespace App\Scopes\Traits;

trait FilialsTraitScopes
{
    // Фильтрация по филиалу
    public function scopeFilials($query, $answer)
    {

      // Получаем из массива answer нужную информацию:
      $dependence = $answer['dependence'];
      $filials = $answer['filials'];

      if($dependence == false){
        
        return $query;

      } else {

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

}
