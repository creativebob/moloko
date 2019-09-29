<?php

namespace App\Observers;

use App\ArticlesGroup;
use App\Observers\Traits\Commonable;

class ArticlesGroupObserver
{
    use Commonable;

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
