<?php

namespace App\Models\Project\Traits;

trait Publicable
{
    /**
     * Проверка display
     *
     * @param $query
     * @param bool $value
     * @param null $table
     */
    public function scopeDisplay($query, $value = true, $table = null)
    {
        $query->where(isset($table) ? "{$table}.display" : 'display', $value);
    }

    /**
     * Проверка archive
     *
     * @param $query
     * @param bool $value
     * @param null $table
     */
    public function scopeArchive($query, $value = false, $table = null)
    {
        $query->where(isset($table) ? "{$table}.archive" : 'archive', $value);
    }

    /**
     * Проверка draft
     *
     * @param $query
     * @param bool $value
     * @param null $table
     */
    public function scopeDraft($query, $value = false, $table = null)
    {
        $query->where(isset($table) ? "{$table}.draft" : 'draft', $value);
    }

    /**
     * Проверка moderation
     *
     * @param $query
     * @param bool $value
     * @param null $table
     */
    public function scopeModeration($query, $value = false, $table = null)
    {
        $query->where(isset($table) ? "{$table}.moderation" : 'moderation', $value);
    }
}
