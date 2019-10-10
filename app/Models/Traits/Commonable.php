<?php

namespace App\Models\Traits;

use App\Company;
use App\User;

trait Commonable
{

    // Компания
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Автор
    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
