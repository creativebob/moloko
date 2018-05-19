<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\SoftDeletes;

// Фильтры
use App\Scopes\Traits\CompaniesLimitTraitScopes;
use App\Scopes\Traits\AuthorsTraitScopes;
use App\Scopes\Traits\SystemItemTraitScopes;
use App\Scopes\Traits\FilialsTraitScopes;
use App\Scopes\Traits\TemplateTraitScopes;
use App\Scopes\Traits\ModeratorLimitTraitScopes;
use App\Scopes\Traits\ContragentsTraitScopes;

class Contragent extends Model
{

    use Notifiable;
    // use SoftDeletes;

    // Подключаем Scopes для главного запроса
    use CompaniesLimitTraitScopes;
    use AuthorsTraitScopes;
    use SystemItemTraitScopes;
    use FilialsTraitScopes;
    use TemplateTraitScopes;
    use ModeratorLimitTraitScopes;
    use ContragentsTraitScopes;
		
  	// protected $dates = ['deleted_at'];
    protected $fillable = [
	    'company_id', 
	    'contragent_id', 
      'client_status', 
      'vendor_status',
    ];




    public function getVendorStatusAttribute($value) {
        if($value == 1){
            $value = 'Поставщик';
            return $value;
        } else 
        {
            return $value;
        };
    }

    public function getClientStatusAttribute($value) {
        if($value == 1){
          $value = 'Клиент';
          return $value;
        } else 
            {
                return $value;
            };
    }


    /**
  * Получаем компанию.
  */
  public function company()
  {
    return $this->belongsTo('App\Company');
  }

    /**
  * Получаем компанию.
  */
  public function contragent()
  {
    return $this->belongsTo('App\Company', 'contragent_id');
  }

  /**
  * Получаем автора
  */
   public function author()
  {
    return $this->belongsTo('App\User', 'author_id');
  }




}
