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



                $mass_lead_types = [];
                if(extra_right('lead-regular')){$mass_lead_types[] = 1;};
                if(extra_right('lead-service')){$mass_lead_types[] = 3;};
                if(extra_right('lead-dealer')){$mass_lead_types[] = 2;};

                if(extra_right('lead-free')){$robot = null;} else {$robot = 1;};
                if(extra_right('lead-all-managers')){$all_managers = 1;} else {$all_managers = null;};

                return $query
                ->where(function($query) use ($mass_lead_types, $robot, $user, $all_managers) {

                    // Если только свои и робота
                    if(($all_managers == null)&&($robot == null)){
                        $query = $query->whereIn('manager_id', [$user->id, 1]);
                    }

                    // Если только свои и без робота
                    if(($all_managers == null)&&($robot == 1)){
                        $query = $query->where('manager_id', $user->id);
                    }

                    // Если без ограничений и без робота
                    if(($all_managers == 1)&&($robot == 1)){
                        $query = $query->where('manager_id', '!=', 1);
                    }

                    // Если без ограничений и c роботом
                    // Ни как не ограничиваем

                    $query
                    ->whereIn('lead_type_id', $mass_lead_types);
                });




        } else {

            return $query;
        }


    }
}
