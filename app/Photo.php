<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

// Scopes для главного запросаФ
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
use Illuminate\Database\Eloquent\Relations\MorphMany;


// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

class Photo extends Model
{

    // Включаем кеш
    use Cachable;

    use Notifiable;

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

    protected $fillable = [
        'name',
        'title',
        'description',

        'path',
        'link',
        'color',

        'width',
        'height',
        'size',
        'extension',

        'access',
        'album_id',

        'display',
        'system',
        'moderation'
    ];



    public function cur_news()
    {
        return $this->hasOne(News::class);
    }

    public function avatar()
    {
        return $this->hasOne(Album::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function albums()
    {
        return $this->morphToMany(Album::class, 'album_entity');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
