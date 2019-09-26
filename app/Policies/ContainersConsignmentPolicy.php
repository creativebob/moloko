<?php

namespace App\Policies;

use App\User;
use App\ContainersConsignment as Model;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Policies\Traits\PoliticTrait;

class ContainersConsignmentPolicy
{
    use HandlesAuthorization;
    use PoliticTrait;

    protected $entity_name = 'consigments_consignments';
    protected $entity_dependence = true;

    public function index(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'index', $this->entity_dependence);
    }

    public function view(User $user, Model $model)
    {
        return $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);
    }

    public function create(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);
    }

    public function update(User $user, Model $model)
    {
        return $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);
    }

    public function delete(User $user, Model $model)
    {
        return $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);
    }

    public function moderator(User $user, Model $model)
    {
        return $this->getstatus($this->entity_name, $model, 'moderator', $this->entity_dependence);
    }

    public function automoderate(User $user, Model $model)
    {
        return $this->getstatus($this->entity_name, $model, 'automoderate', $this->entity_dependence);
    }

    public function display(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'display', $this->entity_dependence);
    }

    public function system(User $user, Model $model)
    {
        if ($model->system == 1) {
            return false;
        }

        return $this->getstatus($this->entity_name, $model, 'system', $this->entity_dependence);
    }

    public function god(User $user)
    {
        return isset(Auth::user()->god);
    }
}
