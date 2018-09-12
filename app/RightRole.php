<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
    

class RightRole extends Model
{

	// Включаем кеш
    // use Cachable;


    protected $table = 'right_role';
    protected $fillable = [
        'right_id', 
        'role_id', 
    ];
}
