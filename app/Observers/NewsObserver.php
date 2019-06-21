<?php

namespace App\Observers;

use App\News;

use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Observers\Traits\CommonTrait;

class NewsObserver
{

    use CommonTrait;

    public function creating(News $cur_news)
    {
        $this->store($cur_news);

        $slug = $this->getSlug($cur_news);
        $cur_news->slug = $slug;
    }

    public function created(News $cur_news)
    {

        $this->updating($cur_news);

    }

    public function updating(News $cur_news)
    {
        $this->update($cur_news);

        // if ($cur_news->isDirty('name')) {
        //     $slug = $this->getSlug($cur_news);
        //     $cur_news->slug = $slug;
        // }

        // Cохраняем / обновляем фото
        $photo_id = savePhoto(request(), $cur_news);
        $cur_news->photo_id = $photo_id;
        // dd($cur_news);
    }

    public function deleting(News $cur_news)
    {
        $this->destroy($cur_news);
    }

    public function saved(News $cur_news)
    {
        $this->setAlbums($cur_news);
    }

    protected function getSlug(News $cur_news)
    {
        // $request = request();
        // dd($request);

        $slug = Str::slug($cur_news->name);

        // // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right('news', false, 'index');

        // // Главный запрос
        // $count_news = News::moderatorLimit($answer)
        // ->companiesLimit($answer)
        // ->authors($answer)
        // ->systemItem($answer)
        // // ->template($answer)
        // ->where('slug', $slug)
        // ->where('id', '!=', $cur_news->id)
        // ->count();
        // // dd($count_news);

        // if ($count_news) {
        //     $slug .= '-'.$cur_news->id;
        // }
        // dd($slug);
        return $slug;
    }

    protected function setAlbums(News $cur_news)
    {
        $request = request();
        $cur_news->albums()->sync($request->albums);
    }

}
