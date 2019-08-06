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

        $site = Site::where('domain', $domain)->first();
//        dd($site);

        $this->site = $site;
    }

    public function start(Request $request)
    {

        if (is_null($this->site)) {
            return view('project.layouts.app');
        }

    }
}
