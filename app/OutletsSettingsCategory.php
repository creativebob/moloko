<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class OutletsSettingsCategory extends Model
{
    use Cachable;

    public $timestamps = false;

    public function settings()
    {
        return $this->hasMany(OutletsSetting::class, 'category_id');
    }
}
