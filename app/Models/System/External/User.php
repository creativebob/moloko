<?php

namespace App\Models\System\External;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'clients_clients';
    protected $connection = 'mysql2';
    
    public function checks()
    {
        return $this->hasMany(Check::class, 'client_id');
    }
}
