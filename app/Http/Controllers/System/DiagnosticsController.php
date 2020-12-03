<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DiagnosticsController extends Controller
{

    /**
     * DiagnosticsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Функция для диагностики
     */
    public function client_duplication()
    {
        $clients = DB::table('clients')
        ->where('clientable_type', '=', 'App\User')
        ->select(DB::raw('clientable_id, count(clientable_id) AS count'))
        ->groupBy('clientable_id')
        ->having('count', '>', 1)
        ->get();

        dd($clients);
    }
}
