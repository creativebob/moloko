<?php

namespace App\Policies;

use App\User;
use App\Access;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    
    // Проверяем на бога. Имеет приоритет над всеми остльными методами
    // Если true - предоставляем доступ
    // Если null - отправляем на проверку в последующих методах
    // если false - блокируем доступ
    
    public function before($user)
    {
        if (Auth::user()->god == 1) {$result = true;} else {$result = null;};
        return $result;
    }

    public function index(User $user)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);
        return $result = $access->where(['right_action' => 'index-user'])->count() == "1";
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);
        return $result = $access->where(['right_action' => 'view-user'])->count() == "1";
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);
        return $result = $access->where(['right_action' => 'create-user'])->count() == "1";
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, User $myuser)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);

        return $result = $access->where(['right_action' => 'update-user'])->count() == "1";
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);
        return $result = $access->where(['right_action' => 'delete-user'])->count() == "1";
    }
}
