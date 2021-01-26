<?php

namespace App\Observers\System;

use App\PhotoSetting;

class PhotoSettingObserver extends BaseObserver
{
    /**
     * Handle the photoSetting "creating" event.
     *
     * @param PhotoSetting $photoSetting
     */
    public function creating(PhotoSetting $photoSetting)
    {
        $this->store($photoSetting);
    }

    /**
     * Handle the photoSetting "updating" event.
     *
     * @param PhotoSetting $photoSetting
     */
    public function updating(PhotoSetting $photoSetting)
    {
        $this->update($photoSetting);
    }

    /**
     * Handle the photoSetting "deleting" event.
     *
     * @param PhotoSetting $photoSetting
     */
    public function deleting(PhotoSetting $photoSetting)
    {
        $this->destroy($photoSetting);
    }
}
