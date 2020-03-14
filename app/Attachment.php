<?php

namespace App;

use App\Models\System\Traits\Articlable;
use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Attachment extends Model
{
    // Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    use Commonable;
    use Articlable;

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

        'display',
        'system',
        'moderation'
    ];

    // Категория
    public function category()
    {
        return $this->belongsTo(AttachmentsCategory::class);
    }

    // Состоит в составе
    public function compositions()
    {
        return $this->belongsToMany(Article::class, 'article_attachment')
            ->whereHas('cur_goods', function($q) {
                $q->where('archive', false);
            });
    }

    public function in_cleans()
    {
        return $this->belongsToMany(Article::class, 'article_attachment')
        ->where('draft', false)
        ->whereHas('in_goods', function($q) {
            $q->where('archive', false);
        });
    }

    public function in_drafts()
    {
        return $this->belongsToMany(Article::class, 'article_attachment')
        ->where('draft', true)
        ->whereHas('in_goods', function($q) {
            $q->where('archive', false);
        });
    }

    // Склад
    public function stocks()
    {
        return $this->hasMany(AttachmentsStock::class, 'cmv_id');
    }
}
