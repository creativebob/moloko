<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;

class TestController extends Controller
{

    /**
     * TestController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Функция для тестов
     */
    public function test()
    {
        dd(__METHOD__);
    }
}
