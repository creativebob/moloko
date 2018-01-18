<?php

namespace App\Scopes\Traits;

use App\Department;

trait FilialsTraitScopes
{


    // Фильтрация по филиалу
    public function scopeFilials($query, $dependence, $session)
    {

    // ПОКАЗ СПИСКА ПОЛЬЗОВАТЕЛЕЙ ---------------------------------------------------------------------------------------------------------------

    if($dependence)
    {

        // Проверяем в правах (которые записаны в сессию) наличие права на просмотр общего списка пользователей 
        // и отсутствие запрета
        if(isset($session['all_rights']['index-users-allow']) && (!isset($session['all_rights']['index-users-deny'])))
        {

            // Получаем список ID филиалов в которых присутствует право на просмотр списка пользователей
            $filials = collect($session['all_rights']['index-users-allow']['departments'])->keys()->toarray();

            // Получаем читаемый список филиалов для SELECT в формах
            $list_filials = Department::whereIn('id', $filials)->pluck('department_name', 'id');

            // Получаем список ID департаментов в которых присутствует право на просмотр списка пользователей
            $departments = collect($session['all_rights']['index-users-allow']['departments'])->values()->toarray();

        } else {

            // Если нет филиалов, что по идеи быть не должно
            if(!isset($filials)){abort(403, "У вас нет права на просмотр списка пользователей!");};

        };

    } else {

        // Если выборка не зависима
        $filials = null;
    };


      if($filials == null){

        return $query;

      } else {
     
          // dd($filials);
          // Получаем записи авторов которых нам открыли - получаем записи созданные нами - получаем себя
          return $query->whereIn('filial_id', $filials);
      }
    }

}
