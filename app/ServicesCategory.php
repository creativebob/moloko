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

class ServicesCategory extends Model
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

    // Фильтры
    use Filter;
    use BooklistFilter;
    use DateIntervalFilter;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'description',
        'seo_description',
        'parent_id',
        // 'goods_mode_id',
        'category_id',
    ];

    // Вложенные
    public function childs()
    {
        return $this->hasMany(ServicesCategory::class, 'parent_id');
    }

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Услуги
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    // Режим
    // public function mode()
    // {
    //     return $this->belongsTo(GoodsMode::class, 'goods_mode_id');
    // }

    // Аватар
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }


    // Процессы
    public function prcesses()
    {
        return $this->belongsToMany(Process::class, 'services')
        ->where('draft', false)
        ->where('services.archive', false);
    }

    public function workflows()
    {
        return $this->belongsToMany(Workflow::class, 'preset_workflow');
    }

    // Производители
    public function manufacturers()
    {
        return $this->morphToMany(Manufacturer::class, 'categories_manufacturer');
    }

    public function groups()
    {
        return $this->morphToMany(ProcessesGroup::class, 'processes_group_entity');
        // ->where('archive', false);
    }

    // Направление
    public function directions()
    {
        return $this->morphOne(Direction::class, 'category');
        // ->where('archive', false);
    }


    // --------------------------------------- Запросы -----------------------------------------
    public function getIndex($request, $answer)
    {
        return $this->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->template($answer) // Выводим шаблоны альбомов
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();
    }

    public function getItem($id, $answer)
    {
        return $this->moderatorLimit($answer)->findOrFail($id);
    }
}
