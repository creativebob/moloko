<?php

namespace App;

use App\Models\System\Traits\Commonable;
use Carbon\Carbon;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outcome extends Model
{
    use Commonable;
    use SoftDeletes;
    use Cachable;

    protected $dates = [
        'begin_date',
        'end_date'
    ];

    protected $fillable = [
        'category_id',

        'name',
        'description',

        'begin_date',
        'end_date',

        'client_id',
        'photo_id',

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
        if ($value) {
            $this->attributes['end_date'] = Carbon::createFromFormat('d.m.Y', $value);
        }
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Категория
    public function category()
    {
        return $this->belongsTo(OutcomesCategory::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
