<?php

namespace App\Policies;

use App\User;
use App\Campaign;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use App\Policies\Traits\PoliticTrait;

class CampaignPolicy
{
    use HandlesAuthorization;
    use PoliticTrait;

    protected $entity_name = 'campaigns';
    protected $entity_dependence = true;

    public function index(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'index', $this->entity_dependence);
        return $result;
    }

    public function view(User $user, Campaign $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);
        return $result;
    }

    public function create(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);
        return $result;
    }

    public function update(User $user, Campaign $model)
    { 
        $result = $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);
        return $result;
    }

    public function delete(User $user, Campaign $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);
        return $result;
    }
    
    public function moderator(User $user, Campaign $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'moderator', $this->entity_dependence);
        return $result;
    }   

    public function automoderate(User $user, Campaign $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'automoderate', $this->entity_dependence);
        return $result;
    }

    public function display(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'display', $this->entity_dependence);
        return $result;
    }

    public function system(User $user, Campaign $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'system', $this->entity_dependence);
        return $result;
    }
    
    public function god(User $user)
    {
        if(Auth::user()->god){return true;} else {return false;};
    }
}
