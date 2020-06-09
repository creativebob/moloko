<?php

namespace App\Http\Controllers\Project\Traits;

use App\Domain;
use Illuminate\Support\Facades\Cookie;
use App\Site;
use Illuminate\Http\Request;

trait Commonable
{

    public function __construct(Request $request)
    {
        $domain = $request->getHost();

        $site = Site::with([
            'pages_public',
            'company',
            'domains.filials.location.city',
            'navigations' => function ($q) {
                $q->with([
                    'align',
//                            'menus' => function ($q) {
//                                $q->with([
//                                    'page'
//                                ])
//                                ->where('display', true)
//                                ->orderBy('sort');
//                            }
                ])
                    ->where('display', true)
                    ->orderBy('sort');
            }
        ])
            ->whereHas('domains', function ($q) use ($domain) {
                $q->where('domain', request()->getHost());
            })
            ->first();
//        dd($site);

        if ($site) {
            $this->site = $site;
            $this->site->domain = $site->domains->firstWhere('domain', $domain);
            $this->site->filial = $this->site->domain->filials->first();

            // Ловим utm метки
            if (isset($request->utm_source)) {
                Cookie::queue('utm_source', $request->utm_source, 60*60*24);
            }
            if (isset($request->utm_term)) {
                Cookie::queue('utm_term', $request->utm_term, 60*60*24);
            }
            if (isset($request->utm_content)) {
                Cookie::queue('utm_content', $request->utm_content, 60*60*24);
            }
            if (isset($request->utm_campaign)) {
                Cookie::queue('utm_campaign', $request->utm_campaign, 60*60*24);
            }
            if (isset($request->utm_medium)) {
                Cookie::queue('utm_medium', $request->utm_medium, 60*60*24);
            }

            if (isset($request->prom)) {
                Cookie::queue('prom', json_encode($request->prom), 60*60*24);
//                return response()->cookie('prom', json_encode($request->prom), 60*60*24);
            }
        } else {
            abort(404, 'Сайт не существует');
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
