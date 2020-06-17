<?php

namespace App\Models\Project;

use App\EstimatesGoodsItem;
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
        'date'
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
        'is_saled',
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
        return $this->hasMany('App\EstimatesGoodsItem');
    }
}
