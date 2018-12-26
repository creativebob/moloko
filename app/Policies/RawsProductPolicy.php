<?php

namespace App\Policies;

use App\Policies\Traits\PoliticTrait;
use App\User;

use App\RawsProduct;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class RawsProductPolicy
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

    protected $entity_name = 'raws_products';
    protected $entity_dependence = false;

    public function before($user)
    {
        // if (Auth::user()->god == 1) {return true;} else {return null;};
        // return $result;
    }

    public function index(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'index', $this->entity_dependence);
    }

    public function view(User $user, RawsProduct $model)
    {
        return $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);
    }

    public function create(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);
    }

    public function update(User $user, RawsProduct $model)
    {
        return $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);
    }

    public function delete(User $user, RawsProduct $model)
    {
        if ($model->articles->count() > 0) {
            return false;
        }

        if ($model->system_item == 1) {
            return false;
        }
        return $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);
    }

    public function moderator(User $user, RawsProduct $model)
    {
        return $this->getstatus($this->entity_name, $model, 'moderator', $this->entity_dependence);
    }

    public function automoderate(User $user, RawsProduct $model)
    {
        return $this->getstatus($this->entity_name, $model, 'automoderate', $this->entity_dependence);
    }

    public function display(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'display', $this->entity_dependence);
    }

    public function system(User $user, RawsProduct $model)
    {
        return $this->getstatus($this->entity_name, $model, 'system', $this->entity_dependence);

    }

    public function god(User $user)
    {
        return isset(Auth::user()->god);
    }
}
