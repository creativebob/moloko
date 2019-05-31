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

use App\Scopes\Traits\ManufacturersTraitScopes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;

class Process extends Model
{
    // Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    // Включаем Scopes
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;

    use ManufacturersTraitScopes;

    // Фильтры
    use Filter;
    use BooklistFilter;
    // use DateIntervalFilter;

    protected $fillable = [
        'name',
        'description',

        'processes_group_id',
        'processes_type_id',

        'internal',
        'manually',
        'external',

        'manufacturer_id',

        'cost_default',
        'cost_mode',
        'price_default',
        'price_mode',
        'price_rule_id',

        'draft',
        'length',
        'unit_id',
    ];

    // Группа
    public function group()
    {
        return $this->belongsTo(ProcessesGroup::class, 'processes_group_id');
    }

    // Товар
    // public function goods()
    // {
    //     return $this->hasMany(Goods');
    // }

    // Рабочие процессы
    public function workflows()
    {
        return $this->belongsToMany(Workflow::class, 'process_workflow')
        ->withPivot([
            'value',
         ]);
    }

    // Состав (набор)
    // public function set_compositions()
    // {
    //     return $this->morphedByMany(Article', 'articles_values')->withPivot('value');
    // }

    // Производитель
    public function manufacturer()
    {
        return $this->belongsTo(Company::class);
    }

    // Альбом
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Автор
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    // Услуга
    public function service()
    {
        return $this->hasOne(Service::class);
    }

    // Еденица измерения
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }


    // Продолжительность
    public function getLengthAttribute($value)
    {
        if (isset($this->unit_id)) {
            $length = $value / $this->unit->ratio;
        } else {
            $length = $value / $this->group->unit->ratio;
        }

        return $length;
    }
}
