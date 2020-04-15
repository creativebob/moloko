<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
     * Перекеширование
     *
     * @return string
     */
    public function reCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('modelCache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');

        \Artisan::call('optimize');
        \Artisan::call('view:cache');

        return "Кэш очищен и установлен";
    }

    /**
     * Очистка кеша
     *
     * @return string
     */
    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('modelCache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');
        return "Очищен кэш";
    }

    /**
     * Установка кеша
     *
     * @return string
     */
    public function setCache()
    {
        \Artisan::call('optimize');
        \Artisan::call('view:cache');
        return "Кэш установлен";
    }
}
