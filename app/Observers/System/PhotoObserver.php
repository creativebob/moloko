<?php

namespace App\Observers\System;

use App\Photo;
use Illuminate\Support\Facades\Storage;

class PhotoObserver extends BaseObserver
{
    /**
     * Handle the photo "creating" event.
     *
     * @param Photo $photo
     */
    public function creating(Photo $photo)
    {
        $this->store($photo);
    }

    /**
     * Handle the photo "updating" event.
     *
     * @param Photo $photo
     */
    public function updating(Photo $photo)
    {
        if (request()->is_avatar == 1) {
            $photo->album->update([
               'photo_id' => $photo->id
            ]);
        }

        $this->update($photo);
    }

    /**
     * Handle the photo "deleting" event.
     *
     * @param Photo $photo
     */
    public function deleting(Photo $photo)
    {
        $album = $photo->album;
        if ($album->photo_id == $photo->id) {
            $album->update([
                'photo_id' => null
            ]);
        }

        Storage::disk('public')
            ->delete($photo->company_id.'/media/albums/'.$photo->album_id.'/img/'.$photo->name);

        foreach (['small', 'medium', 'large', 'original'] as $value) {
            Storage::disk('public')
                ->delete($photo->company_id.'/media/albums/'.$photo->album_id.'/img/' . $value . '/'.$photo->name);
        }

        $photo->albums()->detach();

        $this->destroy($photo);
    }
}
