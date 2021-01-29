<?php

namespace App\Http\View\Composers\System;

use App\PhotoSetting;
use Illuminate\View\View;

class DefaultPhotoSettingsComposer
{

    /**
     * Настройки для фото по умолчанию
     */
    protected $defaultPhotoSettings;

    /**
     * EntitiesComposer constructor.
     */
    public function __construct()
    {
        $this->defaultPhotoSettings = PhotoSetting::whereNull('company_id')
            ->where('system', true)
            ->first();
    }

    /**
     * Отдаем на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        return $view->with('defaultPhotoSettings', $this->defaultPhotoSettings);
    }
}
