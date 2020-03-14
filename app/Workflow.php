<?php

namespace App;

use App\Models\System\Traits\Commonable;
use App\Models\System\Traits\Processable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// Подключаем кеш
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Workflow extends Model
{

	// Включаем кеш
    use Cachable;

    use Notifiable;
    use SoftDeletes;

    use Commonable;
    use Processable;

    protected $fillable = [
        'category_id',
        'process_id',

        'display',
        'system',
        'moderation'
    ];

    // Процесс
    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    // Категория
    public function category()
    {
        return $this->belongsTo(WorkflowsCategory::class);
    }

    // Состоит в составе
    public function compositions()
    {
        return $this->belongsToMany(Process::class, 'process_workflow')
        ->where('draft', false)
        ->whereHas('service', function($q) {
            $q->where('archive', false);
        });
    }

}
