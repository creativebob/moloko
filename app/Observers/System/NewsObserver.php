<?php

namespace App\Observers\System;

use App\News;

use Illuminate\Support\Str;

use App\Observers\System\Traits\Commonable;

class NewsObserver
{
    use Commonable;

    public function creating(News $cur_news)
    {
        $this->store($cur_news);

    }

    public function created(News $cur_news)
    {
        $this->setSlug($cur_news);
        $cur_news->save();
    }

    public function updating(News $cur_news)
    {
        $this->update($cur_news);
        $this->setSlug($cur_news);
    }

    public function deleting(News $cur_news)
    {
        $this->destroy($cur_news);
    }

    public function saved(News $cur_news)
    {
        $this->setAlbums($cur_news);
    }

    protected function setSlug(News $cur_news)
    {
        // $request = request();
        // dd($request);

        $slug = \Str::slug($cur_news->name);

         // Получаем из сессии необходимые данные (Функция находиться в Helpers)
         $answer = operator_right('news', false, 'index');

         // Главный запрос
         $count_news = News::moderatorLimit($answer)
         ->companiesLimit($answer)
         ->authors($answer)
         ->systemItem($answer)
         // ->template($answer)
         ->where('slug', $slug)
         ->where('id', '!=', $cur_news->id)
         ->count();
         // dd($count_news);

         if ($count_news) {
             $slug .= '-'.$cur_news->id;
         }
        // dd($slug);
        $cur_news->slug = $slug;
    }


    protected function setAlbums(News $cur_news)
    {
        $request = request();
        $cur_news->albums()->sync($request->albums);
    }

}
