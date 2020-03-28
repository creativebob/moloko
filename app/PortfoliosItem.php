<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfoliosItem extends Model
{
    use Commonable;
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'portfolio_id',

        'name',

        'title',
        'description',
        'seo_description',

        'parent_id',
        'photo_id',
        'color',

        'display_mode_id',
        'directive_category_id',

        'is_controllable_mode',
        'is_show_subcategory',

        'display',
        'system',
        'moderation'
    ];

    // Каталог
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function portfolio_public()
    {
        return $this->belongsTo(Portfolio::class)
            ->where('display', true);
    }

    // Родитель
    public function parent()
    {
        return $this->belongsTo(PortfoliosItem::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(PortfoliosItem::class, 'parent_id');
    }

    // Главный
    public function category()
    {
        return $this->belongsTo(PortfoliosItem::class);
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
}
