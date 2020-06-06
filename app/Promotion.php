<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Promotion extends Model
{
    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    use Commonable;

    protected $dates = [
        'deleted_at',
        'begin_date',
        'end_date'
    ];

    protected $fillable = [
        'name',
        'description',
        'begin_date',
        'end_date',
        'link',

        'site_id',

        'photo_id',

        'is_slider',
        'is_recommend',
        'is_upsale',

        'mode',

        'tiny',
        'small',
        'medium',
        'large',
        'large_x',

        'horizontal',
        'vertical',
        'square',

        'prom',

        'display',
        'system',
        'moderation'
    ];

    public function setBeginDateAttribute($value)
    {
        $this->attributes['begin_date'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    public function setEndDateAttribute($value)
    {
        if (isset($value)) {
            $this->attributes['end_date'] = Carbon::createFromFormat('d.m.Y', $value);
        }
    }

    public function getEntityAliasAttribute($value)
    {

        return $this->getTable();
    }

//    public function filials()
//    {
//        return $this->belongsToMany(Department::class, 'filial_promotion', 'promotion_id', 'filial_id');
//    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function prices_goods()
    {
        return $this->belongsToMany(PricesGoods::class, 'promotion_price_goods', 'promotion_id', 'price_goods_id');
    }

    public function filials()
    {
        return $this->belongsToMany(Department::class, 'filial_promotion', 'promotion_id', 'filial_id');
    }

    // Фото
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Фотки
    public function tiny()
    {
        return $this->belongsTo(Photo::class, 'tiny_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function small()
    {
        return $this->belongsTo(Photo::class, 'small_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function medium()
    {
        return $this->belongsTo(Photo::class, 'medium_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function large()
    {
        return $this->belongsTo(Photo::class, 'large_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }

    public function large_x()
    {
        return $this->belongsTo(Photo::class, 'large_x_id')
            ->withDefault([
                'path' => '/img/system/plug/album_small_default.jpg'
            ]);
    }
}
