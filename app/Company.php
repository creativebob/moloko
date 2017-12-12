<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

	public function user_info()
	{
		return $this->BelongsTo('App\User', 'user_id');
	}

    protected $dates = ['deleted_at'];

    protected $fillable = [

        'company_name', 
        'company_phone', 
        'company_extra_phone', 
        'city_id', 
        'company_address', 
        'company_inn', 
        'kpp', 
        'account_settlement', 
        'account_correspondent', 
        'bank', 
        'user_id'

    ];
}
