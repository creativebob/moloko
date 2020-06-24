<?php

namespace App\Models\System\RollHouse;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public $timestamps = false;

    protected $table = 'clients_clients';
    protected $connection = 'mysql2';
    protected $dates = [
        'created',
        'updated'
    ];

    protected $fillable = [
        'is_parse',
    ];

    public function checks()
    {
        return $this->hasMany(Check::class, 'client_id');
    }
}
