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

class Page extends Model
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
    public function setMydateAttribute($value){
        $date_parts = explode('.', $value);
        $this->attributes['mydate'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
    }
    protected $fillable = [
        'name', 
        'site_id ', 
        'title', 
        'description', 
        'alias', 
    ];

    // БЛОК ОПИСАНИЯ ФИЛЬТРОВ:
    // Фильтрация по статусу пользователя: клиент или сотрудник
    public function scopeSiteId($query, $site_id)
    {
        if(isset($site_id)){
        return $query->where('site_id', $site_id);
        }
    }
    // КОНЕЦ БЛОКА ОПИСАНИЯ ФИЛЬТРОВ

    // Получаем сайт.
    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    // Получаем должность страницы.
    public function position()
    {
        return $this->hasOne('App\Position');
    }

    // Получаем пункты меню.
    public function menus()
    {
        return $this->hasMany('App\Menu');
    }

    // Получаем автора
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    // Получаем сущности.
    public function entities()
    {
        return $this->belongsToMany('App\Entity');
    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

}
