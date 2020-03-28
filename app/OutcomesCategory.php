<?php

namespace App;

use App\Models\System\Traits\Commonable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutcomesCategory extends Model
{
    use Commonable;
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'name',
        'description',
        'seo_description',

        'parent_id',
        'photo_id',

        'display',
        'system',
        'moderation'
    ];

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    public function outcomes()
    {
        return $this->hasMany(Outcome::class, 'category_id');
    }

    // Родитель
    public function parent()
    {
        return $this->belongsTo(OutcomesCategory::class);
    }

    // Вложенные
    public function childs()
    {
        return $this->hasMany(OutcomesCategory::class, 'parent_id');
    }

}
