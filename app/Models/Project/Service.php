<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use Publicable;
//    use Cachable;
    use SoftDeletes;

    protected $with = [
        'process',
//        'metrics'
    ];

    // Процесс
    public function process()
    {
        return $this->belongsTo(Process::class)
            //            ->display()
            ->draft();
    }


}
