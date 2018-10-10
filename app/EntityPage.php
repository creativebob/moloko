<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityPage extends Model
{
    protected $table = 'entity_page';
    protected $fillable = [
        'entity_id', 
        'page_id', 
    ];
}
