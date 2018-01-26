<?php

namespace App\Policies;

use App\Policies\Traits\PoliticTrait;
use App\User;
use App\RightsRole;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;
// use App\Http\Controllers\Session;

class UserPolicy
{
    
    use HandlesAuthorization;
    use PoliticTrait;

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
        // if (Auth::user()->god == 1) {$result = true;} else {$result = null;};
        // return $result;
    }

    public function index(User $user)
    {
        $result = $this->getstatus('users', null, 'index');
        return $result;
    }

    public function view(User $user, User $model)
    {
        $result = $this->getstatus('users', $model, 'view');
        return $result;
    }

    public function create(User $user)
    {
        $result = $this->getstatus('users', null, 'create');
        return $result;
    }

    public function update(User $user, User $model)
    { 
        $result = $this->getstatus('users', $model, 'update');
        return $result;
    }

    public function delete(User $user, User $model)
    {
        $result = $this->getstatus('users', $model, 'delete');
        return $result;
    }

    public function god(User $user)
    {
        if(Auth::user()->god){return true;} else {return false;};
    }
}
