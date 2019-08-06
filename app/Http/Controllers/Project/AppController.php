<?php

namespace App\Http\Controllers\Project;

use App\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppController extends Controller
{

    // Настройки контроллера
    public function __construct(Request $request)
    {
//        $domain = $request->getHttpHost();
        $domain = $request->getHost();
//        dd($domain);

        // Убираем последнее расширение после точки в домене, и чистим от лишних символов, чтоб получить алиас
        $str = preg_replace("/\.\w+$/","", $domain);
        $alias = str_replace([' ', '-', '_'], '', $str);
//        dd($alias);

        $this->site = Site::where('domain', $alias)->first();
    }

    public function start(Request $request)
    {

        if (is_null($this->site)) {
            return view('project.layouts.app');
        }

    }
}
