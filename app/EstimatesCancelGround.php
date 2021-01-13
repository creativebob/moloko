<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstimatesCancelGround extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

}
