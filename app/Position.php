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

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Position extends Model
{

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
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'position_name',
        'page_id',
        'direct_status',
        'company_id',
    ];

    // Получаем районы и города области.

    public function page()
    {
        return $this->belongsTo('App\Page');
    }

    public function employees()
    {
        return $this->hasMany('App\Employee');
    }

    // Получаем должность.
    public function staff()
    {
        return $this->hasMany('App\Staffer');
    }

    // Получаем роли.
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем компанию
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    // Получаем сектор должности
    public function sector()
    {
        return $this->belongsTo('App\Sector');
    }
}
