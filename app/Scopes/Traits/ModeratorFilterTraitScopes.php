<?php

namespace App\Scopes\Traits;

trait ModeratorFilterTraitScopes
{

    // Фильтрация записей модерируемых записей по филиалу и автору
    public function scopeModeratorFilter($query, $entity_dependence)
    {
        // Получаем данные из сессии
        $session  = session('access');
        $user_id = $session['user_info']['user_id'];

        if(($entity_dependence == false)&&($entity_dependence == null)){

            $query
            ->orWhere(function ($query) use ($user_id) {$query->whereNull('moderated')->orWhere('moderated', 1);});
            return $query;

        } else {

            $moderator_filials = collect(getLS('users', 'moderator', 'filials'))->keys()->toarray();

            $query
            ->where(function ($query) use ($moderator_filials) {$query->whereNull('moderated')->orwhere('moderated', 1)->WhereIn('filial_id', $moderator_filials);})
            ->Orwhere(function ($query) use ($user_id) {$query->withoutGlobalScope(ModerationScope::class)->Where('moderated', 1)->Where('author_id', $user_id);});
            return $query;
        };
    }
}
