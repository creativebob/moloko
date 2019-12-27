<?php

namespace App\Http\Controllers\Project\Traits;

use App\Domain;
use Illuminate\Support\Facades\Cookie;
use App\Site;
use Illuminate\Http\Request;

trait Commonable
{

    public function __construct()
    {

        $domain = Domain::with([
            'site' => function ($q) {
                $q->with([
                    'pages_public',
                    'company',
                    'domains.filials'
                ]);
            },
            'filials.location.city'
        ])
            ->where('domain', request()->getHost())
            ->first();
//        dd($domain);

        if ($domain) {
            $this->site = $domain->site;
            $this->site->domain = $domain;
            $this->site->filial = $domain->filials->first();
        }

//        $domain = request()->getHost();
//        $paths_arr = explode('.', $domain);
//
//        // Если сайт работает в режиме СУБДОМНОВ
//        if (count($paths_arr) > 2) {
//
//            $city_alias = $paths_arr[0];
//            $main_domain = $paths_arr[1] . '.' . $paths_arr[2];
//
//            $site = Site::where('domain', $main_domain)
//            ->whereHas('filials', function($q) use ($city_alias) {
//                $q->whereHas('location', function($q) use ($city_alias) {
//                    $q->whereHas('city', function($q) use ($city_alias) {
//                        $q->where('alias', $city_alias);
//                    });
//                });
//            })
//            ->with([
//                'pages_public',
//                'filials.location.city',
//                'company'
//            ])->first();
//
//            $this->site = $site;
//            $this->site->filial = $site->filials->where('location.city.alias', $city_alias)->first();
//
//            // Ставим куку города
////            if (Cookie::has('city')) {
////                $cookie_city_alias = Cookie::get('city');
////                if ($cookie_city_alias != $city_alias) {
////                    return redirect()->route('project.change_city', $cookie_city_alias);
////                }
////            }
//
//        // Если сайт работает в режиме БЕЗ СУБДОМЕНОВ
//        } else {
//
//            $site = Site::where('domain', $domain)->with([
//                'pages_public',
//                'filials.location.city',
//                'company'
//            ])
//            ->first();
//
//            $this->site = $site;
//            $this->site->filial = $site->filials->first();
//        }



    }

}
