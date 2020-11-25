<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
    /**
     * CacheController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Очистка кеша
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function clear()
    {
        Artisan::call('cache:clear');
        Artisan::call('modelCache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return __('cache.clear');
    }

    /**
     * Установка кеша
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function set()
    {
        Artisan::call('optimize');
        Artisan::call('view:cache');
        return __('cache.set');
    }

    /**
     * Перекеширование
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function reCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('modelCache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        Artisan::call('optimize');
        Artisan::call('view:cache');

        return __('cache.recache');
    }
}
