<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Session;

class ModerationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {

        // dd($model);
        // Получаем данные из сессии
        $session  = session('access');
        $user_id = $session['user_info']['user_id'];

        $builder->where('moderated', null)->orWhere(function ($builder) use ($user_id) {$builder->Where('moderated', 1)->Where('author_id', $user_id);});
    }
}