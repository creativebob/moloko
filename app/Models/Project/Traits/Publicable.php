<?php


namespace App\Models\Project\Traits;

trait Publicable
{
    public function scopeDisplay($query, $value = true)
    {
        $query->where('display', $value);
    }

    public function scopeArchive($query, $value = false)
    {
        $query->where('archive', $value);
    }

    public function scopeDraft($query, $value = false)
    {
        $query->where('draft', $value);
    }
}
