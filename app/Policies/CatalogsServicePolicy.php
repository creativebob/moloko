<?php

namespace App\Policies;

use App\User;
use App\CatalogsService as Model;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Policies\Traits\PoliticTrait;

class CatalogsServicePolicy
{
    use HandlesAuthorization;
    use PoliticTrait;

    protected $entity_name = 'catalogs_services';
    protected $entity_dependence = false;

    public function index(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'index', $this->entity_dependence);

        return $result;
    }

    public function view(User $user, Model $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);

        return $result;
    }

    public function create(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);

        return $result;
    }

    public function update(User $user, Model $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);

        return $result;
    }

    public function delete(User $user, Model $model)
    {
        if ($model->items->count() > 0) {
            return false;
        }

        $result = $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);

        return $result;
    }

    public function moderator(User $user, Model $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'moderator', $this->entity_dependence);

        return $result;
    }

    public function automoderate(User $user, Model $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'automoderate', $this->entity_dependence);

        return $result;
    }

    public function display(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'display', $this->entity_dependence);

        return $result;
    }

    public function system(User $user, Model $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'system', $this->entity_dependence);

        return $result;
    }

    public function god(User $user)
    {
        $result = isset(Auth::user()->god);

        return $result;
    }
}
