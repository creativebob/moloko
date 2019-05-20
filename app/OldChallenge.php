<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldChallenge extends Model
{
    protected $table = 'challenges';
    protected $connection = 'mysql2';
    
    // Автор
    // public function author()
    // {
    //     return $this->belongsTo('App\OldUser', 'creator_challenge');
    // }
    
    // // Кому назначили
    // public function appointed()
    // {
    //     return $this->belongsTo('App\OldUser', 'id_user_subject');
    // }
    
    // // Кто выполнил
    // public function finisher()
    // {
    //     return $this->belongsTo('App\OldUser', 'user_finish');
    // }
    
    // // Этап
    // public function stage()
    // {
    //     return $this->belongsTo('App\OldStage', 'id_stage_ini');
    // }
    
    // // Этап
    // public function task()
    // {
    //     return $this->belongsTo('App\OldTask', 'id_model_challenge');
    // }
    
    // Получаем тип задачи
    public function challenge_type()
    {
        return $this->belongsTo('App\ChallengesType', 'challenges_type_id');
    }

    // // Получаем права категории.
    // public function rights()
    // {
    //     return $this->hasMany('App\Rights');
    // }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем пользователя, кому назначена задача
    public function appointed()
    {
        return $this->belongsTo('App\User', 'appointed_id');
    }

    // Получаем пользователя, завершившего задачу
    public function finisher()
    {
        return $this->belongsTo('App\User', 'finisher_id');
    }

    // Получаем все
    // public function subject()
    // {
    //     return $this->morphTo();
    // }	
}
