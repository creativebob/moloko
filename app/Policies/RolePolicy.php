<?php

namespace App\Policies;

use App\Policies\Traits\PoliticTrait;
use App\User;
use App\Role;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
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
    
    protected $entity_name = 'roles';
    protected $entity_dependence = false;

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
        $result = $this->getstatus($this->entity_name, null, 'index', $this->entity_dependence);
        return $result;
    }

    public function view(User $user, Role $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);
        return $result;
    }

    public function create(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);
        return $result;
    }

    public function update(User $user, Role $model)
    { 
        $result = $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);
        return $result;
    }

    public function delete(User $user, Role $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);
        return $result;
    }

    public function moderator(User $user, Role $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'moderator', $this->entity_dependence);
        return $result;
    }

    public function automoderate(User $user, Role $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'automoderate', $this->entity_dependence);
        return $result;
    }

    public function display(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'display', $this->entity_dependence);
        return $result;
    }

    public function system(User $user, Role $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'system', $this->entity_dependence);
        return $result;
    }

    public function god(User $user)
    {
        if(Auth::user()->god){return true;} else {return false;};
    }
}
