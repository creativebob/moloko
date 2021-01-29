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
            ->where('system', true)
            ->first([
                'photo_settings_id',
                'photo_settings_type',

                'store_format',
                'quality',

                'img_min_width',
                'img_min_height',

                'img_small_width',
                'img_small_height',

                'img_medium_width',
                'img_medium_height',

                'img_large_width',
                'img_large_height',

                'img_formats',
                'img_max_size',

                'strict_mode',
                'crop_mode',
            ])
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
