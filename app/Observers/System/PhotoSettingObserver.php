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

    /**
     * Handle the photoSetting "saving" event.
     * Если какие то настройки не заполнены, берем их из умолчаний
     *
     * @param PhotoSetting $photoSetting
     */
    public function saving(PhotoSetting $photoSetting)
    {
        $defaultSettings = PhotoSetting::whereNull('company_id')
            ->first()
            ->toArray();
//        dd($defaultSettings);

        if ($defaultSettings) {
            foreach ($defaultSettings as $setting => $value) {
                // Если есть ключ в пришедших настройках, то переписываем значение
                if (is_null($photoSetting->$setting)) {
                    $photoSetting->$setting = $defaultSettings[$setting];
                }
            }
        }
//        dd($photoSetting);
    }
}
