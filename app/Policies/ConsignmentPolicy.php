<?php

namespace App\Policies;

use App\User;
use App\Consignment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Policies\Traits\PoliticTrait;

class ConsignmentPolicy
{
    use HandlesAuthorization;
    use PoliticTrait;

    protected $entity_name = 'consignments';
    protected $entity_dependence = true;

    public function index(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'index', $this->entity_dependence);
    }

    public function view(User $user, Consignment $model)
    {
        return $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);
    }

    public function create(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);
    }

    public function update(User $user, Consignment $model)
    {
        return $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);
    }

    public function delete(User $user, Consignment $model)
    {
        return $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);
    }

    public function moderator(User $user, Consignment $model)
    {
        return $this->getstatus($this->entity_name, $model, 'moderator', $this->entity_dependence);
    }

    public function automoderate(User $user, Consignment $model)
    {
        return $this->getstatus($this->entity_name, $model, 'automoderate', $this->entity_dependence);
    }

    public function display(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'display', $this->entity_dependence);
    }

    public function system(User $user, Consignment $model)
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
