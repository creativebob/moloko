<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogsService extends Model
{
    use Publicable;
    use Cachable;
    use SoftDeletes;

    // Разделы
    public function items()
    {
        return $this->hasMany(CatalogsServicesItem::class)
            ->display();
    }

    // Прайсы
    public function prices()
    {
        return $this->hasMany(PricesService::class)
            ->display()
            ->archive()
            ->has('service');
    }

    // Филиалы
    public function filials()
    {
        return $this->belongsToMany('App\Department', 'catalogs_service_filial', 'catalogs_service_id', 'filial_id');
    }

}
