<?php

namespace App\Scopes\Traits;

trait ManagerTraitScopes
{

    // Фильтрация для показа авторов
    public function scopeManager($query, $user)
    {
        return $query->WhereIn('manager_id', [$user->id, 1]);
    }
}
