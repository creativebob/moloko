<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogsGoods extends Model
{
    use Publicable;
    use Cachable;
    use SoftDeletes;

    protected $table = 'catalogs_goods';

    // Разделы
    public function items()
    {
        return $this->hasMany(CatalogsGoodsItem::class)
            ->display()
            ->orderBy('sort');
    }

    // Прайсы
    public function prices()
    {
        return $this->hasMany(PricesGoods::class)
            ->display()
            ->archive()
            ->has('goods')
            ->orderBy('sort');
    }

    // Филиалы
    public function filials()
    {
        return $this->belongsToMany('App\Department', 'catalogs_goods_filial', 'catalogs_goods_id', 'filial_id');
    }
}
