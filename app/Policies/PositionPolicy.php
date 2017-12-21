<?php

namespace App\Policies;

use App\User;
use App\Position;
use App\Access;
use Illuminate\Auth\Access\HandlesAuthorization;

class PositionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the position.
     *
     * @param  \App\User  $user
     * @param  \App\Position  $position
     * @return mixed
     */
    // Проверяем на бога. Имеет приоритет над всеми остльными методами
    // Если true - предоставляем доступ
    // Если null - отправляем на проверку в последующих методах
    // если false - блокируем доступ
    
    public function before($user)
    {
      if ($user->god == 1) {$result = true;} else {$result = null;};
      return $result;
    }

    public function index(User $user)
    {
      // return Access::whereAccess_group_id($user->group_action_id)
      //               ->whereRight_action('update-position')
      //               ->first();
    }
    /**
     * Determine whether the user can view the position.
     *
     * @param  \App\User  $user
     * @param  \App\Position  $position
     * @return mixed
     */
    public function view(User $user, Position $position)
    {
      
    }

    /**
     * Determine whether the user can create positions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
      $access = Access::whereAccess_group_id($user->group_action_id);
      return $access->whereRight_action('create-position')->first();
    }

    /**
     * Determine whether the user can update the position.
     *
     * @param  \App\User  $user
     * @param  \App\Position  $position
     * @return mixed
     */
    public function update(User $user, Position $position)
    {
      $access = Access::whereAccess_group_id($user->group_action_id);
      return $access->whereRight_action('update-position')->first();
    }

    /**
     * Determine whether the user can delete the position.
     *
     * @param  \App\User  $user
     * @param  \App\Position  $position
     * @return mixed
     */
    public function delete(User $user, Position $position)
    {
      $access = Access::whereAccess_group_id($user->group_action_id);
      return $access->whereRight_action('delete-position')->first();
    }
}
