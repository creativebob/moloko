<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Reserve extends Model
{

    // Включаем кеш
    use Cachable;

    use SoftDeletes;

    use Commonable;

    protected $fillable = [
        'stock_id',
        'filial_id',
        'document_id',
        'document_type',
        'documents_item_id',
        'documents_item_type',
        'cmv_id',
        'cmv_type',
        'count',

    ];

    // Тмц
    public function cmv()
    {
        return $this->morphTo();
    }

    // История
    public function history()
    {
        return $this->hasMany(ReservesHistory::class, 'reserve_id');
    }
}
