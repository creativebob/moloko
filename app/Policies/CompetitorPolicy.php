<?php

namespace App\Policies;

use App\User;
use App\Competitor as Model;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\Traits\PoliticTrait;

class CompetitorPolicy
{
    use HandlesAuthorization;
    use PoliticTrait;

    protected $entityName = 'competitors';
    protected $entityDependence = false;

    public function index(User $user)
    {
        $result = $this->getstatus($this->entityName, null, 'index', $this->entityDependence);
        return $result;
    }

    public function view(User $user, Model $model)
    {
        $result = $this->getstatus($this->entityName, $model, 'view', $this->entityDependence);
        return $result;
    }

    public function create(User $user)
    {
        $result = $this->getstatus($this->entityName, null, 'create', $this->entityDependence);
        return $result;
    }

    public function update(User $user, Model $model)
    {
        $result = $this->getstatus($this->entityName, $model, 'update', $this->entityDependence);
        return $result;
    }

    public function delete(User $user, Model $model)
    {
        $result = $this->getstatus($this->entityName, $model, 'delete', $this->entityDependence);
        return $result;
    }

    public function moderator(User $user, Model $model)
    {
        $result = $this->getstatus($this->entityName, $model, 'moderator', $this->entityDependence);
        return $result;
    }

    public function automoderate(User $user, Model $model)
    {
        $result = $this->getstatus($this->entityName, $model, 'automoderate', $this->entityDependence);
        return $result;
    }

    public function display(User $user)
    {
        $result = $this->getstatus($this->entityName, null, 'display', $this->entityDependence);
        return $result;
    }

    public function system(User $user, Model $model)
    {
        $result = $this->getstatus($this->entityName, $model, 'system', $this->entityDependence);
        return $result;
    }

    public function god()
    {
        $result = isset(auth()->user()->god);
        return $result;
    }
}
