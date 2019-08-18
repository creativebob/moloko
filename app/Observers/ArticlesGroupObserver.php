<?php

namespace App\Observers;

use App\ArticlesGroup;
use App\Observers\Traits\CommonTrait;

class ArticlesGroupObserver
{
    use CommonTrait;

    public function creating(ArticlesGroup $articles_group)
    {
        $this->store($articles_group);
    }

    public function updating(ArticlesGroup $articles_group)
    {
        $this->update($articles_group);
    }

    public function deleting(ArticlesGroup $articles_group)
    {
        $this->destroy($articles_group);
    }
}
