<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientsBlacklist extends Model
{
    use Cachable;
    use SoftDeletes;
    use Commonable;

    protected $dates = [
        'begin_date',
        'end_date',
    ];

    protected $fillable = [
        'begin_date',
        'end_date'
    ];

    // Клиент
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
