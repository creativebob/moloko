<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldClaim extends Model
{
    protected $table = 'claim';
    protected $connection = 'mysql2';
    
    // Манеджер
    public function user()
    {
        return $this->belongsTo('App\OldUser', 'id_user');
    }
}
