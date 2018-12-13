<?php

namespace App\Policies;

use App\User;
use App\AlbumsCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Policies\Traits\PoliticTrait;

class AlbumsCategoryPolicy
{
    use HandlesAuthorization;
    use PoliticTrait;

    protected $entity_name = 'albums_categories';
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

    public function view(User $user, AlbumsCategory $model)
    {
        return $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);
    }

    public function create(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);
    }

    public function update(User $user, AlbumsCategory $model)
    {
        return $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);
    }

    public function delete(User $user, AlbumsCategory $model)
    {
        if ($model->system_item == 1) {
            return false;
        }

        // if ($model->company_id == null) {
        //     return false;
        // }

        if ($model->albums->count() > 0) {
            return false;
        }

        return $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);
    }

    public function moderator(User $user, AlbumsCategory $model)
    {
        return $this->getstatus($this->entity_name, $model, 'moderator', $this->entity_dependence);
    }

    public function automoderate(User $user, AlbumsCategory $model)
    {
        return $this->getstatus($this->entity_name, $model, 'automoderate', $this->entity_dependence);
    }

    public function display(User $user)
    {
        return $this->getstatus($this->entity_name, null, 'display', $this->entity_dependence);
    }

    public function system(User $user, AlbumsCategory $model)
    {
        return $this->getstatus($this->entity_name, $model, 'system', $this->entity_dependence);
    }

    public function god(User $user)
    {
        return isset(Auth::user()->god);
    }
}
