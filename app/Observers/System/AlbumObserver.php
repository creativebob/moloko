<?php

namespace App\Observers\System;

use App\Album;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Observers\System\Traits\Commonable;

class AlbumObserver extends BaseObserver
{
    /**
     * Handle the album "creating" event.
     *
     * @param Album $album
     */
    public function creating(Album $album)
    {
        $this->store($album);
    }

    /**
     * Handle the album "created" event.
     *
     * @param Album $album
     */
    public function created(Album $album)
    {
        // Создаем папку в файловой системе
        $storage = Storage::disk('public')
            ->makeDirectory($album->company_id . '/media/albums/' . $album->id);

        if (!$storage) {
            abort(403, 'Ошибка создания папки альбома');
        }
    }

    /**
     * Handle the album "updating" event.
     *
     * @param Album $album
     */
    public function updating(Album $album)
    {
        $this->update($album);
    }

    /**
     * Handle the album "deleting" event.
     *
     * @param Album $album
     */
    public function deleting(Album $album)
    {
        $this->destroy($album);
    }

    /**
     * Handle the album "deleted" event.
     *
     * @param Album $album
     */
    public function deleted(Album $album)
    {

        // Удаляем папку альбома
        $directory = $album->company_id.'/media/albums/'.$album->id;
        $del_dir = Storage::disk('public')
            ->deleteDirectory($directory);

        // Удаляем фотки
        $album->photos()->delete();
        $album->photo_settings()->delete();
    }

//    protected function setSlug(Album $album)
//    {
//        if (empty($album->slug)) {
//            $album->slug = Str::slug($album->name);
//        }
//    }
}
