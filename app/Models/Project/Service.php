<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use App\Models\Project\Traits\Publicable;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Publicable;
    use Cachable;

    protected $with = [
        'process',
        'metrics'
    ];

    // Процесс
    public function process()
    {
        return $this->belongsTo(Process::class)
            //            ->display()
            ->draft();
    }


}
