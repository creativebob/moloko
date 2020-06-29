<?php

namespace App\Http\Controllers\System;

use App\Exports\Sheets\RollHouseSheet;
use App\Models\System\RollHouse\AuthCustomuser;
use App\Models\System\RollHouse\Check;

use App\Http\Controllers\Controller;
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
//        $checks = Check::where('client_id', 27374)
//            ->where('progress', 2)
//            ->whereNull('employer_id')
//            ->get();
//        dd($checks->sum('summa'));
        dd(__METHOD__);
    }
}
