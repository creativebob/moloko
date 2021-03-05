<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Stocks\ContainersStock;
use App\Models\System\Traits\Cmvable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Container extends BaseModel
{
    // Включаем кеш
//    use Cachable;

    use Notifiable;
    use SoftDeletes;
    use Cmvable;

    protected $fillable = [
        'category_id',
        'article_id',
        'unit_for_composition_id',
        'price_unit_id',
        'price_unit_category_id',

        'portion_goods_status',
        'portion_goods_name',
        'portion_goods_abbreviation',
        'unit_portion_goods_id',
        'portion_goods_count',

        'archive',
        'serial',

        'display',
        'system',
        'moderation'
    ];

    // Категория
    public function category()
    {
        return $this->belongsTo(ContainersCategory::class);
    }

    // Состоит в составе
    public function compositions()
    {
        return $this->belongsToMany(Article::class, 'article_container')
            ->whereHas('cur_goods', function($q) {
                $q->where('archive', false);
            });
    }

    public function in_cleans()
    {
        return $this->belongsToMany(Article::class, 'article_container')
        ->where('draft', false)
        ->whereHas('in_goods', function($q) {
            $q->where('archive', false);
        });
    }

    public function in_drafts()
    {
        return $this->belongsToMany(Article::class, 'article_container')
        ->where('draft', true)
        ->whereHas('in_goods', function($q) {
            $q->where('archive', false);
        });
    }

    // Склад
    public function stocks()
    {
        return $this->hasMany(ContainersStock::class, 'cmv_id');
    }
}
