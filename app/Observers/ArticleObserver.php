<?php

namespace App\Observers;

use App\Article;

class ArticleObserver
{

    public function creating(Article $article)
    {

        $article->draft = true;

        $user = request()->user();

        $article->company_id = $user->company_id;
        $article->author_id = hideGod($user);
    }

    public function updating(Article $article)
    {
        $request = request();
        // dd($request);

        // Проверки только для черновика
        if ($article->getOriginal('draft') == 1) {

            // Определяем количество метрик
            // $metrics_count = isset($request->metrics) ? count($request->metrics) : 0;
            // $article->metrics_count = $metrics_count;

            // // Определяем количество составов
            // $compositions_count = isset($request->compositions) ? count($request->compositions) : 0;
            // $article->compositions_count = $compositions_count;

            // Порции
            if ($request->has('portion_status')) {
                $article->portion_status = $request->portion_status;
                $article->portion_name = $request->portion_name;
                $article->portion_abbreviation = $request->portion_abbreviation;
                $article->portion_count = $request->portion_count;
            } else {
                $article->portion_status = false;
            }
        }

        $article->editor_id = hideGod($request->user());

        // Cохраняем / обновляем фото
        $photo_id = savePhoto($request, $article);
        $article->photo_id = $photo_id;
    }
}
