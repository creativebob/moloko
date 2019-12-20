<?php

namespace App;

use App\Models\Traits\Commonable;
use Carbon\Carbon;
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

class Promotion extends Model
{
    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    use Commonable;

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

    protected $dates = [
        'deleted_at',
        'begin_date',
        'end_date'
    ];

    protected $fillable = [
        'name',
        'description',
        'begin_date',
        'end_date',
        'link',

        'photo_id',

        'tiny',
        'small',
        'medium',
        'large',
        'large_x',

        'display',
        'system',
        'moderation'
    ];

    public function setBeginDateAttribute($value)
    {
        $this->attributes['begin_date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    public function setEndDateAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['end_date'] = Carbon::createFromFormat('d.m.Y', $value);
        }
    }

    public function getEntityAliasAttribute($value)
    {

        return $this->getTable();
    }

    public function filials()
    {
        return $this->belongsToMany(Department::class, 'filial_promotion', 'promotion_id', 'filial_id');
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'promotion_site', 'promotion_id', 'site_id')
//            ->withPivot([
//                'filial_id'
//            ])
            ;
    }

    // Фото
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Фотки
    public function tiny()
    {
        return $this->belongsTo(Photo::class, 'tiny_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function small()
    {
        return $this->belongsTo(Photo::class, 'small_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function medium()
    {
        return $this->belongsTo(Photo::class, 'medium_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function large()
    {
        return $this->belongsTo(Photo::class, 'large_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function large_x()
    {
        return $this->belongsTo(Photo::class, 'large_x_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }
}
