<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Reserve extends BaseModel
{
    
    use SoftDeletes;
    use Cachable;

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
