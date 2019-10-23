<?php

namespace App\Models\Project;

use App\EstimatesGoodsItem;
use App\Lead;
use App\Models\Traits\Commonable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


class Estimate extends Model
{

    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    protected $dates = [
        'deleted_at',
        'date'
    ];
    protected $fillable = [
        'lead_id',
        'client_id',
	    'filial_id',
        'company_id',
	
	    'date',
        
        'number',
        'author_id',
        'draft',
        'is_saled',
	    'amount',
	    'total',
	    'discount',
	    'discount_percent',
	    

    ];

    // Лид
    public function lead()
    {
        return $this->belongsTo('App\Lead');
    }

    // Товары
    public function goods_items()
    {
        return $this->hasMany('App\EstimatesGoodsItem');
    }
}
