<?php

namespace App\Http\Controllers\System;

use App\Client;
use App\ClientsIndicator;
use App\Company;
use App\Console\Commands\ClientsIndicatorsCommand;
use App\Estimate;
use App\EstimatesGoodsItem;
use App\Exports\LeadsExport;
use App\Http\Controllers\Controller;
use App\Unit;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArticlesExport;

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


        $res = \Artisan::call('clients-indicators:report 2020-01-01');
        dd($res == 0);
//        return Excel::download(new ArticlesExport, 'Товары для себестоимости.xlsx');
    }
}
