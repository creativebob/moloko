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
                return $query->WhereIn('manager_id', [$user->id, 1]);
            }

        } else {

            return $query;
        }



    }
}
