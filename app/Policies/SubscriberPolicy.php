<?php

namespace App\Policies;

use App\User;
use App\Subscriber as Item;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\Traits\PoliticTrait;

class SubscriberPolicy
{
    use HandlesAuthorization;
    use PoliticTrait;

    protected $entityName = 'subscribers';
    protected $entityDependence = false;

    public function index(User $user)
    {
        $result = $this->getstatus($this->entityName, null, 'index', $this->entityDependence);
        return $result;
    }

    public function view(User $user, Item $item)
    {
        $result = $this->getstatus($this->entityName, $item, 'view', $this->entityDependence);
        return $result;
    }

    public function create(User $user)
    {
        $result = $this->getstatus($this->entityName, null, 'create', $this->entityDependence);
        return $result;
    }

    public function update(User $user, Item $item)
    {
        $result = $this->getstatus($this->entityName, $item, 'update', $this->entityDependence);
        return $result;
    }

    public function delete(User $user, Item $item)
    {
        $result = $this->getstatus($this->entityName, $item, 'delete', $this->entityDependence);
        return $result;
    }

    public function moderator(User $user, Item $item)
    {
        $result = $this->getstatus($this->entityName, $item, 'moderator', $this->entityDependence);
        return $result;
    }

    public function automoderate(User $user, Item $item)
    {
        $result = $this->getstatus($this->entityName, $item, 'automoderate', $this->entityDependence);
        return $result;
    }

    public function display(User $user)
    {
        $result = $this->getstatus($this->entityName, null, 'display', $this->entityDependence);
        return $result;
    }

    public function system(User $user, Item $item)
    {
        $result = $this->getstatus($this->entityName, $item, 'system', $this->entityDependence);
        return $result;
    }

    public function god()
    {
        $result = isset(auth()->user()->god);
        return $result;
    }
}
