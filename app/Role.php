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
// use App\Scopes\Filters\Filter;
// use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Role extends Model
{

    // Включаем кеш
    // use Cachable;

    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    // Фильтры
    // use Filter;
    // use BooklistFilter;
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id',
        'role_name',
        'role_description',
        'category_right_id',

        'display',
        'system',
        'moderation',

        // TODO - 13.04.20 - Удалить, разлочено для обновы
        'company_id'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User')->withPivot('department_id');
    }

    // Получаем права.
    public function rights()
    {
        return $this->belongsToMany('App\Right');
    }

    // Получаем категорию.
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем категорию.
    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    public function positions()
    {
        return $this->belongsToMany('App\Position', 'role_user', 'role_id', 'position_id');
    }

    public function departments()
    {
        return $this->belongsToMany('App\Department', 'role_user', 'role_id', 'department_id');
    }

}
