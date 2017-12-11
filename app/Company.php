<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $dates = ['deleted_at'];

    protected $fillable = [

        'company_name', 
        'company_inn', 
        'kpp', 
        'account_settlement', 
        'account_correspondent', 
        'bank', 
        'user_id'

    ];
}
