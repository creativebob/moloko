<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    protected $fillable = [
        'title',
        'h1',

        'description',
        'keywords',
        'content',

        'is_canonical',
        'parent_id',
    ];

    protected $casts = [
        'is_canonical' => 'boolean'
    ];

    public function childs()
    {
        return $this->hasMany(Seo::class, 'parent_id');
    }

    public function params()
    {
        return $this->hasMany(SeosParam::class);
    }
}
