<?php

namespace App\Filters;

class SiteFilter extends QueryFilter
{
    public function author($id)
    {
        return $this->builder->where('author_id', $id);
    }

    public function company($brandIds)
    {
        return $this->builder->whereIn('company_id', $this->paramToArray($brandIds));
    }

    public function search($keyword)
    {
        return $this->builder->where('domain', 'like', '%'.$keyword.'%');
    }

}