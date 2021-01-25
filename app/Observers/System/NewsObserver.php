<?php

namespace App\Observers\System;

use App\News;
use Illuminate\Support\Facades\Storage;

class NewsObserver extends BaseObserver
{
    /**
     * Handle the curNews "creating" event.
     *
     * @param News $curNews
     */
    public function creating(News $curNews)
    {
        $this->store($curNews);
    }

    /**
     * Handle the curNews "created" event.
     *
     * @param News $curNews
     */
    public function created(News $curNews)
    {
        $this->setNewsSlug($curNews);
        $curNews->save();
    }

    /**
     * Handle the curNews "updating" event.
     *
     * @param News $curNews
     */
    public function updating(News $curNews)
    {
        $this->setNewsSlug($curNews);
        $this->update($curNews);
    }

    /**
     * Handle the curNews "deleting" event.
     *
     * @param News $curNews
     */
    public function deleting(News $curNews)
    {
        // Удаляем связи
        $curNews->albums()->detach();
        $curNews->photo()->delete();

        // Удаляем файлы
        $directory = "{$curNews->company_id}/media/news/{$curNews->id}";
        $del_dir = Storage::disk('public')
            ->deleteDirectory($directory);

        $this->destroy($curNews);
    }

    /**
     * Handle the curNews "saved" event.
     *
     * @param News $curNews
     */
    public function saved(News $curNews)
    {
        $this->setAlbums($curNews);
    }

    /**
     * Слаг
     *
     * @param News $curNews
     */
    protected function setNewsSlug(News $curNews)
    {
        // $request = request();
        // dd($request);

        $slug = \Str::slug($curNews->name);

         // Получаем из сессии необходимые данные (Функция находиться в Helpers)
         $answer = operator_right('news', false, 'index');

         // Главный запрос
         $count_news = News::moderatorLimit($answer)
         ->companiesLimit($answer)
         ->authors($answer)
         ->systemItem($answer)
         // ->template($answer)
         ->where('slug', $slug)
         ->where('id', '!=', $curNews->id)
         ->count();
         // dd($count_news);

         if ($count_news) {
             $slug .= '-'.$curNews->id;
         }
        // dd($slug);
        $curNews->slug = $slug;
    }

    /**
     * Связь новости с альбомами
     *
     * @param News $curNews
     */
    protected function setAlbums(News $curNews)
    {
        $request = request();
        $curNews->albums()->sync($request->albums);
    }
}
