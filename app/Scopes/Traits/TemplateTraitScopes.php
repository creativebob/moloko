<?php

namespace App\Scopes\Traits;

trait TemplateTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeTemplate($query, $answer)
    {

        return $query
        ->orWhere(function ($query) {$query
        ->whereNull('company_id')
        ->where('system', false)
        ;});
        
    }

}
