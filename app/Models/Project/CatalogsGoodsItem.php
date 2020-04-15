<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;

class CatalogsGoodsItem extends Model
{
    use Publicable;
    use Cachable;

    // Каталог
    public function catalog()
    {
        return $this->belongsTo(CatalogsGoods::class, 'catalogs_goods_id')
            ->display();
    }

    // Родитель
    public function parent()
    {
        return $this->belongsTo(CatalogsGoodsItem::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(CatalogsGoodsItem::class, 'parent_id');
    }

    // Главный
    public function category()
    {
        return $this->belongsTo(CatalogsGoodsItem::class);
    }

    // Прайсы
    public function prices()
    {
        return $this->hasMany(PricesGoods::class)
            ->display()
            ->archive()
            ->has('goods');
    }

    public function childs_prices_public()
    {
        return $this->hasManyThrough(PricesGoods::class, CatalogsGoodsItem::class, 'parent_id', 'catalogs_goods_item_id')
            ->has('goods_public')
            ->where([
                'prices_goods.display' => true,
                'prices_goods.archive' => false
            ]);
    }

    public function getNameWithParentAttribute()
    {
        if($this->parent_id != null){
            return $this->parent->name . ' / ' . $this->name;
        } else {
            return $this->name;
        }
    }

}
