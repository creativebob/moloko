<?php

namespace App\Http\Controllers\Project\Traits;

use App\Site;
use Illuminate\Http\Request;

trait Commonable
{

    public function __construct()
    {

        $domain = request()->getHost();
        $paths_arr = explode('.', $domain);

        // Если сайт работает в режиме СУБДОМНОВ
        if (count($paths_arr) > 2) {

            $city_alias = $paths_arr[0];
            $main_domain = $paths_arr[1] . '.' . $paths_arr[2];

            $site = Site::where('domain', $main_domain)
            ->whereHas('filials', function($q) use ($city_alias) {
                $q->whereHas('location', function($q) use ($city_alias) {
                    $q->whereHas('city', function($q) use ($city_alias) {
                        $q->where('alias', $city_alias);
                    });
                });
            })
            ->with([
                'pages_public',
                'filials.location.city',
                'company'
            ])->first();

        // Если сайт работает в режиме БЕЗ СУБДОМЕНОВ
        } else {
            
            $site = Site::where('domain', $domain)->with([
                'pages_public',
                'filials.location.city',
                'company'
            ])
            ->first();
        }

        $this->site = $site;
        $this->site->filial = $site->filials->where('location.city.alias', $city_alias)->first();

    }

}