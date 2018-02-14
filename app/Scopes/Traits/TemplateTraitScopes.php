<?php

namespace App\Scopes\Traits;

trait TemplateTraitScopes
{

    // Фильтрация для показа системных записей
    public function scopeTemplate($query, $answer)
    {

        return $query
        ->orWhere(function ($query) {$query
        ->Where('company_id', null)            
        ->Where('system_item', null)
        ;});
        
    }

}
