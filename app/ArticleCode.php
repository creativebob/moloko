<?php

namespace App;

use App\Models\Project\Article;
use App\Models\System\BaseModel;

class ArticleCode extends BaseModel
{

    protected $touches = [
        'article'
    ];

    protected $fillable = [
        'name',
        'description',

        'article_id',

        'display',
        'system',
        'moderation'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
