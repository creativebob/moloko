<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientsIndicator extends Model
{

    use Cachable;
    use SoftDeletes;
    use Commonable;


    protected $dates = [
        'start_date',
    ];

    protected $fillable = [
        'start_date',
        'unit_id',
        'author_id',
        'company_id',

        'count',
        'active_count',
        'active_previous_count',
        'lost_count',
        'deleted_count',
        'blacklist_count',
        'new_clients_period_count',
        'lost_clients_period_count',
        'customer_retention_rate',
        'churn_rate',
        'customers_period_count',
        'orders_count',
        'orders_period_count',
        'lead_close_rate',
        'repeat_purchase_rate',
        'purchase_frequency',
        'purchase_frequency_period',
        'order_gap_analysis',
        'orders_revenue',
        'orders_revenue_period',
        'arpu',
        'arppu',
        'paying_share',
        'lifetime',
        'lifetime_fact',
        'average_order_value',
        'average_order_value_period',
        'customer_value',
        'customer_value_period',
        'ltv',
        'ltv_period',
        'customer_equity',
        'nps',
    ];
}
