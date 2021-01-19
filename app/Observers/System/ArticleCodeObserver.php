<?php

namespace App\Observers\System;

use App\ArticleCode;

class ArticleCodeObserver extends BaseObserver
{
    /**
     * Handle the articleCode "creating" event.
     *
     * @param ArticleCode $articleCode
     */
    public function creating(ArticleCode $articleCode)
    {
        $this->store($articleCode);
    }

    /**
     * Handle the articleCode "updating" event.
     *
     * @param ArticleCode $articleCode
     */
    public function updating(ArticleCode $articleCode)
    {
        $this->update($articleCode);
    }

    /**
     * Handle the articleCode "deleting" event.
     *
     * @param ArticleCode $articleCode
     */
    public function deleting(ArticleCode $articleCode)
    {
        $this->destroy($articleCode);
    }
}
