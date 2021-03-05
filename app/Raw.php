<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Stocks\RawsStock;
use App\Models\System\Traits\Cmvable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Raw extends BaseModel
{
    use SoftDeletes,
        Cmvable;
    //    use Cachable;

    const ALIAS = 'raws';
    const DEPENDENCE = false;

//    protected $appends   = [
//        'cost_portion',
//    ];

    protected $fillable = [
        'category_id',
        'article_id',

        'unit_for_composition_id',
        'price_unit_id',
        'price_unit_category_id',

        'portion_status',
        'portion_name',
        'portion_abbreviation',
        'unit_portion_id',
        'portion_count',

        'archive',
        'serial',

        'display',
        'system',
        'moderation'
    ];

    public function category()
    {
        return $this->belongsTo(RawsCategory::class);
    }

    public function stocks()
    {
        return $this->hasMany(RawsStock::class, 'cmv_id');
    }

    // Состоит в составе
    public function compositions()
    {
        return $this->belongsToMany(Article::class, 'article_raw')
            ->whereHas('cur_goods', function ($q) {
                $q->where('archive', false);
            });
    }

    public function in_cleans()
    {
        return $this->belongsToMany(Article::class, 'article_raw')
            ->where('draft', false)
            ->with([
                'cur_goods'
            ])
            ->whereHas('cur_goods', function ($q) {
                $q->where('archive', false);
            });
    }

    public function in_drafts()
    {
        return $this->belongsToMany(Article::class, 'article_raw')
            ->where('draft', true)
            ->with([
                'cur_goods'
            ])
            ->whereHas('cur_goods', function ($q) {
                $q->where('archive', false);
            });
    }



    public function getNameAttribute()
    {
        $value = $this->article->name;
        return $value;
    }

}
