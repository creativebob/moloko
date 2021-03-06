<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientsLoyaltiesScore extends Model
{
    use Cachable;
    use SoftDeletes;
    use Commonable;

    protected $fillable = [
        'loyalty_score',
    ];

    // Клиент
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
