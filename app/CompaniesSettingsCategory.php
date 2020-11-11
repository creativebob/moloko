<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class CompaniesSettingsCategory extends Model
{
    use Cachable;

    public $timestamps = false;

    public function settings()
    {
        return $this->hasMany(CompaniesSetting::class, 'category_id');
    }
}
