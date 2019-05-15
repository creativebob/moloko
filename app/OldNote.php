<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldNote extends Model
{
    protected $table = 'notes';
    protected $connection = 'mysql2';
    
    // Автор
    public function author()
    {
        return $this->belongsTo('App\OldUser');
    }
    
   
}
