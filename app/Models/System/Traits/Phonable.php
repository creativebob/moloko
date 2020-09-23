<?php

namespace App\Models\System\Traits;

use App\Phone;

trait Phonable
{

    public function phones()
    {
        return $this->morphToMany(Phone::class, 'phone_entity');
    }


    public function main_phones()
    {
        return $this->morphToMany(Phone::class, 'phone_entity')
            ->wherePivot('main', 1)
            ->whereNull('archive')
            ->withPivot([
                'main',
                'archive'
            ]);
    }


    public function getMainPhoneAttribute()
    {
        if (!empty($this->main_phones->first())) {
            $value = $this->main_phones->first();
        } else {
            $value = null;
        }
        return $value;
    }

    // Дополнительные
    public function extra_phones()
    {
        return $this->morphToMany(Phone::class, 'phone_entity')
            ->whereNull('archive')
            ->whereNull('main')
            ->withPivot('archive');
    }

}
