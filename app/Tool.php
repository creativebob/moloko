<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Cmvable;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Tool extends BaseModel
{
    use SoftDeletes,
        Cmvable;
    //    use Cachable;

    const ALIAS = 'tools';
    const DEPENDENCE = false;

    protected $fillable = [
        'category_id',
        'article_id',

        'serial',

        'tools_type_id',

        'display',
        'system',
        'moderation'
    ];

    // Категория
    public function category()
    {
        return $this->belongsTo(ToolsCategory::class);
    }

    // Автор
    public function type()
    {
        return $this->belongsTo(ToolsType::class, 'tools_type_id');
    }
}
