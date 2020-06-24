<?php

namespace App\Models\System\RollHouse;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    public $timestamps = false;
    protected $table = 'checks_checks';
    protected $connection = 'mysql2';
    protected $dates = [
        'created'
    ];

    protected $fillable = [
        'is_parse',
    ];

    public function consists()
    {
        return $this->hasMany(ChecksConsist::class, 'check_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
