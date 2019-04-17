<?php

namespace App\Observers;

use App\Article;

class ArticleObserver
{

    public function updating(Article $article)
    {
        // $this->setName($article);
    }

    protected function setName(Article $article)
    {
        dd($article->name);
        if ($article->isDirty('name')) {
                $article->name = $article->name;
            }
    }


}
