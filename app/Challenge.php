<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
    

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;
use App\Scopes\Filters\StatusFilter;

class Challenge extends Model
{
    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    use DateIntervalFilter;
    use StatusFilter;

    // public $timestamps = false;
    
    protected $dates = ['deleted_at', 'deadline_date', 'completed_date'];
    protected $fillable = [
        'company_id', 'description', 'appointed_id', 'finisher_id', 'author_id', 'deadline_date', 'status', 'completed_date', 'challenges_type_id', 'created_at'
    ];

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
    public function challenges()
    {
        return $this->morphTo();
    }

    public function getStatusResultAttribute($value) {

        if($this->status == 1){
            return 'Выполнена';
        } else {
            return 'Не выполнена';
        }
    }
}
