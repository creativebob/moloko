<?php

namespace App\Models\Project;

use App\Models\Project\Traits\Commonable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Promotion extends Model
{
    use Commonable;
    use Publicable;
    use Cachable;


    protected $dates = [
        'deleted_at',
        'begin_date',
        'end_date'
    ];

    public function prices_goods()
    {
        return $this->belongsToMany(PricesGoods::class, 'promotion_price_goods', 'promotion_id', 'price_goods_id');
    }

    public function filials()
    {
        return $this->belongsToMany('App\Department', 'filial_promotion', 'promotion_id', 'filial_id');
    }

    // Фото
    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }

    // Фотки
    public function tiny()
    {
        return $this->belongsTo('App\Photo', 'tiny_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function small()
    {
        return $this->belongsTo('App\Photo', 'small_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function medium()
    {
        return $this->belongsTo('App\Photo', 'medium_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function large()
    {
        return $this->belongsTo('App\Photo', 'large_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function large_x()
    {
        return $this->belongsTo('App\Photo', 'large_x_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }
}
