<?php

namespace App\Policies;

use App\User;
use App\CatalogProduct;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Policies\Traits\PoliticTrait;

class CatalogProductPolicy
{
    use HandlesAuthorization;
    use PoliticTrait;

    protected $entity_name = 'catalog_products';
    protected $entity_dependence = false;

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

    public function view(User $user, CatalogProduct $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);
        return $result;
    }

    public function create(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);
        return $result;
    }

    public function update(User $user, CatalogProduct $model)
    { 
        $result = $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);
        return $result;
    }

    public function delete(User $user, CatalogProduct $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);
        return $result;
    }

    public function moderator(User $user, CatalogProduct $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'moderator', $this->entity_dependence);
        return $result;
    }

    public function automoderate(User $user, CatalogProduct $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'automoderate', $this->entity_dependence);
        return $result;
    }

    public function display(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'display', $this->entity_dependence);
        return $result;
    }

    public function system(User $user, CatalogProduct $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'system', $this->entity_dependence);
        return $result;
    }
    
    public function god(User $user)
    {
        if(Auth::user()->god){return true;} else {return false;};
    }
}
