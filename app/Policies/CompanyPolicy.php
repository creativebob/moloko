<?php

namespace App\Policies;

use App\Policies\Traits\PoliticTrait;
use App\User;
use App\Company;
use App\Supplier;
use App\Dealer;
use App\Manufacturer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    
    use HandlesAuthorization;
    use PoliticTrait;

    protected $entity_name = 'companies';
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

    public function view(User $user, Company $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'view', $this->entity_dependence);
        return $result;
    }

    public function create(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'create', $this->entity_dependence);
        return $result;
    }

    public function update(User $user, Company $model)
    { 
        $result = $this->getstatus($this->entity_name, $model, 'update', $this->entity_dependence);
        return $result;
    }

    public function delete(User $user, Company $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'delete', $this->entity_dependence);

        // Проверяем на наличие связей
        if(($model->we_suppliers->count() > 0)||($model->we_manufacturers->count() > 0)||($model->we_dealers->count() > 0)){$result = false;};

        // $suppliers = Supplier::where('company_id', $model->id)->orWhere('contragent_id', $model->id);
        // $dealers = Dealer::where('company_id', $model->id)->orWhere('contragent_id', $model->id);
        // $manufacturers = Manufacturer::where('company_id', $model->id)->orWhere('contragent_id', $model->id);

        // // Если есть связи - запрещаем удаление
        // if(($suppliers->count() > 0)||($dealers->count() > 0)||($manufacturers->count() > 0)){
        //     return false;
        // };

        return $result;
    }

    public function moderator(User $user, Company $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'moderator', $this->entity_dependence);
        return $result;
    }

    public function automoderate(User $user, Company $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'automoderate', $this->entity_dependence);
        return $result;
    }

    public function display(User $user)
    {
        $result = $this->getstatus($this->entity_name, null, 'display', $this->entity_dependence);
        return $result;
    }

    public function system(User $user, Company $model)
    {
        $result = $this->getstatus($this->entity_name, $model, 'system', $this->entity_dependence);
        return $result;
    }
    
    public function god(User $user)
    {
        if(Auth::user()->god){return true;} else {return false;};
    }
      
}
