<?php

namespace App\Models\Project;

use App\Models\System\Documents\EstimatesGoodsItem;
use App\Lead;
use App\Models\Project\Traits\Publicable;
use App\Models\System\Traits\Commonable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


class Estimate extends Model
{

    use Publicable;
    use Cachable;
    use SoftDeletes;

    protected $dates = [
        'deleted_at',
        'date',
        'registered_at',
        'conducted_at'
    ];
    protected $fillable = [
        'lead_id',
        'client_id',
	    'filial_id',
        'stock_id',

        'company_id',
        'author_id',

	    'date',

        'number',
        'cost',

        'draft',

        'conducted_at',
        'registered_at',
        'is_main',

	    'amount',
	    'total',

        'margin_percent',
        'margin_currency',

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
        return $this->hasMany('App\Models\System\Documents\EstimatesGoodsItem');
    }

    // Услуги
    public function services_items()
    {
        return $this->hasMany('App\Models\System\Documents\EstimatesServicesItem');
    }

    public function discounts()
    {
        return $this->belongsToMany('App\Discount');
    }

    public function catalogs_goods()
    {
        return $this->belongsToMany('App\CatalogsGoods', 'catalogs_goods_estimate');
    }

    public function catalogs_services()
    {
        return $this->belongsToMany('App\CatalogsService');
    }
}
