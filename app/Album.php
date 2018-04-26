<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Заготовки
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Фильтры
use App\Scopes\Filters\CityFilter;
use App\Scopes\Filters\BooklistFilter;

class Album extends Model
{
  use Notifiable;
  use SoftDeletes;

  // Подключаем Scopes для главного запроса
  use CompaniesLimitTraitScopes;
  use AuthorsTraitScopes;
  use SystemItemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorLimitTraitScopes;
  use BooklistFilter;

  protected $dates = ['deleted_at'];

  protected $fillable = [

  ];

	// Получаем компанию
  public function company()
  {
    return $this->belongsTo('App\Company');
  }

  // Получаем категорию
  public function albums_category()
  {
    return $this->belongsTo('App\AlbumsCategory');
  }

  // Получаем фото
  public function photos()
  {
    return $this->belongsToMany('App\Photo', 'album_media', 'album_id', 'media_id')->where('entity', 'photo');
  }

    // Получаем видео
    // public function videos()
    // {
    // return $this->belongsToMany('App\Video', 'album_media', 'media_id')->where('entity', 'video');
    // }

	// Получаем автора
  public function author()
  {
   return $this->belongsTo('App\User', 'author_id');
  }

}
