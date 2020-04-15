<?php


namespace App\Models\Project\Traits;

trait Publicable
{
    /**
     * Скопа display
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
     * Скопа archive
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
     * Скопа draft
     *
     * @param $query
     * @param bool $value
     * @param null $table
     */
    public function scopeDraft($query, $value = false, $table = null)
    {
        $query->where(isset($table) ? "{$table}.draft" : 'draft', $value);
    }
}
