<?php

namespace App;

use App\Models\System\Traits\Articlable;
use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Raw extends Model
{

    use Commonable;
    use SoftDeletes;
    use Cachable;
    use Articlable;

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

        'display',
        'system',
        'moderation'
    ];

    // Категория
    public function category()
    {
        return $this->belongsTo(RawsCategory::class);
    }

    // Состоит в составе
    public function compositions()
    {
        return $this->belongsToMany(Article::class, 'article_raw')
        ->whereHas('cur_goods', function($q) {
            $q->where('archive', false);
        });
    }

    public function in_cleans()
    {
        return $this->belongsToMany(Article::class, 'article_raw')
        ->where('draft', false)
        ->whereHas('in_goods', function($q) {
            $q->where('archive', false);
        });
    }

    public function in_drafts()
    {
        return $this->belongsToMany(Article::class, 'article_raw')
        ->where('draft', true)
        ->whereHas('in_goods', function($q) {
            $q->where('archive', false);
        });
    }

    // Склад
    public function stocks()
    {
        return $this->hasMany(RawsStock::class, 'cmv_id');
    }

    public function getNameAttribute() {
        $value = $this->article->name;
        return $value;
    }

}
