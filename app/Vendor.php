<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Vendor extends Model
{
    // Включаем кеш
    use Cachable;

    use Commonable;

    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
         'supplier_id',

        'status',
        'description',

        'display',
        'system',
        'moderation'
    ];


    // Поставщик
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getVendoredAttribute()
    {
        return $this->company_id == auth()->user()->company_id && $this->archive == false;
    }

    public function files()
    {
        return $this->morphToMany(File::class, 'entity', 'file_entities');
    }
}
