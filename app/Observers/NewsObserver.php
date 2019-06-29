<?php

namespace App\Observers;

use App\News;

use Illuminate\Support\Str;

use App\Observers\Traits\CommonTrait;

class NewsObserver
{
    use CommonTrait;

    public function creating(News $cur_news)
    {
        $this->store($cur_news);

    }

    public function created(News $cur_news)
    {
        $this->setPhotoId($cur_news);
        $this->setSlug($cur_news);
        $cur_news->save();
    }

    public function updating(News $cur_news)
    {
        $this->update($cur_news);
        $this->setPhotoId($cur_news);
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

    protected function setPhotoId(News $cur_news)
    {
        $request = request();
        $photo_id = savePhoto($request, $cur_news);
        $cur_news->photo_id = $photo_id;

    }

    protected function setAlbums(News $cur_news)
    {
        $request = request();
        $cur_news->albums()->sync($request->albums);
    }

}