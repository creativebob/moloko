<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldHistory extends Model
{
   protected $table = 'history';
    protected $connection = 'mysql2';
    
    
    // Автор
    public function user()
    {
        return $this->belongsTo('App\OldUser', 'id_user');
    }
    
    
    
}
