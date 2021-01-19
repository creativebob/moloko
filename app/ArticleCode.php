<?php

namespace App;

use App\Models\System\BaseModel;

class ArticleCode extends BaseModel
{
    protected $fillable = [
        'name',
        'description',

        'article_id',

        'display',
        'system',
        'moderation'
    ];
}
