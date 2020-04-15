<?php


namespace App\Models\Project\Traits;

trait Publicable
{
    public function scopeDisplay($query, $value = true)
    {
        $query->where('display', $value);
    }

    public function scopeArchive($query, $value = true)
    {
        $query->where('archive', $value);
    }
}
