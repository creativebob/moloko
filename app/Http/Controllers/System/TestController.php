<?php

namespace App\Http\Controllers\System;

use App\Exports\Sheets\RollHouseSheet;
use App\Models\System\RollHouse\AuthCustomuser;
use App\Models\System\RollHouse\Check;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Facades\Excel;

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
