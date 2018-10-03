<?php

namespace App\Scopes\Traits;

trait ManagerTraitScopes
{

    // Фильтрация для показа авторов
    public function scopeManager($query, $user)
    {

        // Получаем сессию
        // $session  = session('access');
        // if(!isset($session)){
        // 	abort(403, 'Нет сессии!');
        // };

        if($user->staff->first() != null){

            if($user->staff->first()->position->id == 4){
                return $query;
            } else {

                $mass_lead_types = [];
                if(extra_right('lead-regular')){$mass_lead_types[] = 1;};
                if(extra_right('lead-service')){$mass_lead_types[] = 3;};
                if(extra_right('lead-dealer')){$mass_lead_types[] = 2;};

                return $query
                ->where('manager_id', $user->id)
                ->orWhere(function($query) use ($mass_lead_types) {
                    $query
                    ->where('manager_id', 1)
                    ->whereIn('lead_type_id', $mass_lead_types);
                });
            }

        } else {

            return $query;
        }


    }
}
