<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldLead extends Model
{
    protected $table = 'leads';
    protected $connection = 'mysql2';

    public $timestamps = false;
    
    // Комментарии
    public function comments()
    {
        return $this->hasMany('App\OldHistory', 'id_lead');
    }
    
    // Рекламации
    public function claims()
    {
        return $this->hasMany('App\OldClaim', 'id_lead');
    }
    
    // Менеджер
    public function manager()
    {
        return $this->belongsTo('App\OldUser', 'id_manager');
    }
    
    // Задача
    public function task()
    {
        return $this->belongsTo('App\OldTask', 'id_task');
    }
    
    // Этап
    public function stage()
    {
        return $this->belongsTo('App\OldStage', 'id_stage');
    }
    
    // Город
    public function city()
    {
        return $this->belongsTo('App\OldCity', 'id_city');
    }
    
    // Выбор
    public function service()
    {
        return $this->belongsTo('App\OldService', 'id_service');
    }
    
    // Задачи
    public function challenges()
    {
        return $this->hasMany('App\OldChallenge', 'id_lead');
    }
}
