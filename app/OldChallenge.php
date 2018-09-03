<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldChallenge extends Model
{
    protected $table = 'challenges';
    protected $connection = 'mysql2';
    
    // Автор
    public function author()
    {
        return $this->belongsTo('App\OldUser', 'creator_challenge');
    }
    
    // Кому назначили
    public function appointed()
    {
        return $this->belongsTo('App\OldUser', 'id_user_subject');
    }
    
    // Кто выполнил
    public function finisher()
    {
        return $this->belongsTo('App\OldUser', 'user_finish');
    }
    
    // Этап
    public function stage()
    {
        return $this->belongsTo('App\OldStage', 'id_stage_ini');
    }
    
    // Этап
    public function task()
    {
        return $this->belongsTo('App\OldTask', 'id_model_challenge');
    }
    
    	
}
