<?php

namespace App\Models\Project\Traits;

trait Commonable
{
    /**
     * Проверка company_id
     *
     * @param $query
     * @param number $companyId
     * @param null $table
     */
    public function scopeCompany($query, $companyId, $table = null)
    {
        $query->where(isset($table) ? "{$table}.company_id" : 'company_id', $companyId);
    }

    /**
     * Проверка site_id
     *
     * @param $query
     * @param number $siteId
     * @param null $table
     */
    public function scopeSite($query, $siteId, $table = null)
    {
        $query->where(isset($table) ? "{$table}.site_id" : 'site_id', $siteId);
    }

}
