<?php

namespace App\Policies;

use App\Right;
use App\User;
use App\Access;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class RightPolicy
{
    use HandlesAuthorization;


    // public function before($user)
    // {
    //     if (Auth::user()->god == 1) {$result = true;} else {$result = null;};
    //     return $result;
    // }

    public function index(User $user, Right $right)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);
        return $result = $access->where(['right_action' => 'index-right'])->count() == "1";
    }

    /**
     * Determine whether the user can view the right.
     *
     * @param  \App\User  $user
     * @param  \App\Right  $right
     * @return mixed
     */
    public function view(User $user, Right $right)
    {
        //
    }

    /**
     * Determine whether the user can create rights.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $current_access = Auth::user()->group_action_id;
        $access = Access::where(['access_group_id' => $current_access]);
        return $result = $access->where(['right_action' => 'create-right'])->count() == "1";
    }

    /**
     * Determine whether the user can update the right.
     *
     * @param  \App\User  $user
     * @param  \App\Right  $right
     * @return mixed
     */
    public function update(User $user, Right $right)
    {
        //
    }

    /**
     * Determine whether the user can delete the right.
     *
     * @param  \App\User  $user
     * @param  \App\Right  $right
     * @return mixed
     */
    public function delete(User $user, Right $right)
    {
        //
    }
}
