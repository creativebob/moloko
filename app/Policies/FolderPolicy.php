<?php

namespace App\Policies;

use App\Folder;
use App\Policies\Traits\PoliticTrait;
use App\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
{
    
    use HandlesAuthorization;
    use PoliticTrait;
    
    protected $entity_name = 'folders';
    protected $entity_dependence = true;
    
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

    public function view(User $user, Folder $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);
        return $result;
    }

    public function create(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);
        return $result;
    }

    public function update(User $user, Folder $model)
    { 
        $result = $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);
        return $result;
    }

    public function delete(User $user, Folder $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);
        return $result;
    }

    public function god(User $user)
    {
        if(Auth::user()->god){return true;} else {return false;};
    }
}
