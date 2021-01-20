<?php

namespace App\Models\Project;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class ArticleCode extends Model
{
    use Cachable;

    protected $touches = [
        'article'
    ];

    public function group()
    {
        return $this->belongsTo(Article::class);
    }
}
