<?php

namespace App\Policies;

use App\User;
use App\Entity;
use App\Access;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the entity.
     *
     * @param  \App\User  $user
     * @param  \App\Entity  $entity
     * @return mixed
     */
    
    public function index(User $user, Entity $entity)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);
        return $result = $access->where(['right_action' => 'index-entity'])->count() == "1";
    }



    public function view(User $user, Entity $entity)
    {

    }

    /**
     * Determine whether the user can create entities.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);
        return $result = $access->where(['right_action' => 'create-entity'])->count() == "1";
    }

    /**
     * Determine whether the user can update the entity.
     *
     * @param  \App\User  $user
     * @param  \App\Entity  $entity
     * @return mixed
     */
    public function update(User $user, Entity $entity)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);
        return $result = $access->where(['right_action' => 'update-entity'])->count() == "1";
    }

    /**
     * Determine whether the user can delete the entity.
     *
     * @param  \App\User  $user
     * @param  \App\Entity  $entity
     * @return mixed
     */
    public function delete(User $user, Entity $entity)
    {
        //
    }
}
