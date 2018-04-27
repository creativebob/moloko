<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;

class News extends Model
{
   use SoftDeletes;
  // Подключаем Scopes для главного запроса
  use CompaniesLimitTraitScopes;
  use AuthorsTraitScopes;
  use SystemItemTraitScopes;
  use FilialsTraitScopes;
  use TemplateTraitScopes;
  use ModeratorLimitTraitScopes;
  /**
   * Атрибуты, которые должны быть преобразованы в даты.
   *
   * @var array
   */
  protected $dates = ['deleted_at'];
  protected $fillable = [
    'name',
    'alias',
    'company_id',
  ];

  public function setDatePublishBeginAttribute($value) {
    if($value == Null){
        return $value;
    } else {
        $date_parts = explode('.', $value);
        $this->attributes['date_publish_begin'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
    };
  }

  public function getDatePublishBeginAttribute($value) {
    if($value == Null){
        return $value;
    } else {
      $date_parts = explode('-', $value);
      $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
      return $value;
    };
  }

  public function setDatePublishEndAttribute($value) {
    if($value == Null){
        return $value;
    } else {
        $date_parts = explode('.', $value);
        $this->attributes['date_publish_end'] = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
    };
  }

  public function getDatePublishEndAttribute($value) {
    if($value == Null){
        return $value;
    } else {
      $date_parts = explode('-', $value);
      $value = $date_parts[2].'.'.$date_parts[1].'.'.$date_parts[0];
      return $value;
    };
  }

  /**
  * Получаем сайт.
  */
  public function site()
  {
    return $this->belongsTo('App\Site');
  }

  /**
  * Получаем компанию.
  */
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
