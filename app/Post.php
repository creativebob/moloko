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


// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
use App\Scopes\Filters\DateIntervalFilter;

class Post extends Model
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

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'alias',
        'company_id',
    ];

    public function setPublishBeginDateAttribute($value) {
        if($value == Null){
            return $value;
        } else {
            $date_parts = explode('.', $value);
            $this->attributes['publish_begin_date'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
        };
    }

    public function getPublishBeginDateAttribute($value) {
        if($value == Null){
            return $value;
        } else {
            $date_parts = explode('-', $value);
            $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
            return $value;
        };
    }

    public function setPublishEndDateAttribute($value) {
        if($value == Null){
            return $value;
        } else {
            $date_parts = explode('.', $value);
            $this->attributes['publish_end_date'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
        };
    }

    public function getPublishEndDateAttribute($value) {
        if($value == Null){
            return $value;
        } else {
            $date_parts = explode('-', $value);
            $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
            return $value;
        };
    }

    // Получаем компанию.
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем альбом
    public function albums()
    {
        return $this->belongsToMany('App\Album', 'album_entity', 'entity_id', 'album_id')->where('entity', 'news');
    }


}
