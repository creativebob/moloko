<?php

namespace App\Models\Project;

use App\Models\Project\Traits\Publicable;
use App\Models\System\BaseModel;
use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class EstimatesServicesItem extends BaseModel
{
    use Publicable;
    use Commonable;
    use Cachable;
    use SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'estimate_id',
        'price_id',

        'service_id',
        'currency_id',
        'sale_mode',

        'comment',

        'cost_unit',
        'price',
        'points',
        'count',

        'cost',
        'amount',

        'price_discount_id',
        'price_discount_unit',

        'catalogs_item_discount_id',
        'catalogs_item_discount_unit',

        'estimate_discount_id',
        'estimate_discount_unit',

        'client_discount_percent',
        'client_discount_unit_currency',
        'client_discount_currency',
        'total_client_discount',

        'total',
        'total_points',
        'total_bonuses',

        'computed_discount_percent',
        'computed_discount_currency',
        'total_computed_discount',

        'is_manual',
        'manual_discount_percent',
        'manual_discount_currency',
        'total_manual_discount',

        'discount_currency',
        'discount_percent',

        'agent_id',
        'agency_scheme_id',

        'share_percent',

        'margin_currency_unit',
        'margin_percent_unit',
        'margin_currency',
        'margin_percent',

        'flow_id',

        'sort',

        'display',
        'system',
        'moderation',
    ];

    // Смета
    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function document()
    {
        return $this->belongsTo(Estimate::class, 'estimate_id');
    }

    // Прайс
    public function price()
    {
        return $this->belongsTo('App\PricesService');
    }

    // Услуга
    public function service()
    {
        return $this->belongsTo('App\Service');
    }

    public function product()
    {
        return $this->belongsTo('App\Service', 'service_id');
    }

    public function process()
    {
        return $this->belongsTo('App\Service', 'service_id');
    }

    public function agency_scheme()
    {
        return $this->belongsTo('App\AgencyScheme');
    }


    public function flow()
    {
        return $this->belongsTo('App\Models\System\Flows\ServicesFlow');
    }

}
