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

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Position extends Model
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
    // use DateIntervalFilter;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'page_id',
        'sector_id',
        'direct_status',

        'display',
        'system',
        'moderation'
    ];

    // Получаем районы и города области.

    public function page()
    {
        return $this->belongsTo(Page::class)
            ->withDefault([
                'alias' => 'Нет страницы'
            ]);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Получаем должность.
    public function staff()
    {
        return $this->hasMany(Staffer::class);
    }

    public function actual_staff()
    {
        return $this->hasMany(Staffer::class)
            ->whereNotNull('user_id');
    }

    // Получаем роли.
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Получаем компанию
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Получаем сектор должности
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    // Оповещения
    public function notifications()
    {
        return $this->belongsToMany(Notification::class);
    }

    // Обязанности
    public function charges()
    {
        return $this->belongsToMany(Charge::class);
    }

    // Виджеты
    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'widget_position');
    }
}
