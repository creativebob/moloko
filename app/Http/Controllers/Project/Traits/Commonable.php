<?php


namespace App\Http\Controllers\Project\Traits;


use App\Site;
use Illuminate\Http\Request;

trait Commonable
{

    /**
     * Получаем сайт по домену, проверяем на наличие поддомена, и на основании этого получаем филиал
     *
     * @param Request $request
     */
    public function __construct()
    {
        $domain = request()->getHost();
        $arr = explode('.', $domain);
//        dd($arr);

        if (count($arr) > 2) {
            $city_alias = $arr[0];
//            dd($city_alias);
            $domain = $arr[1] . '.' . $arr[2];
//            dd($domain);

            $site = Site::where('domain', $domain)
                ->with([
                    'pages_public',
                    'filials' => function($q) use ($city_alias) {
                        $q->whereHas('location', function($q) use ($city_alias) {
                            $q->whereHas('city', function($q) use ($city_alias) {
                                $q->where('alias', $city_alias);
                            });
                        });
                    }
                ])
//                ->when()
                ->whereHas('filials', function($q) use ($city_alias) {
                    $q->whereHas('location', function($q) use ($city_alias) {
                        $q->whereHas('city', function($q) use ($city_alias) {
                            $q->where('alias', $city_alias);
                        });
                    });
                })
                ->first();
//            dd($site);
        } else {
            $city_alias = null;

            $site = Site::where('domain', $domain)
                ->with([
                    'pages_public',
                    'filials.location.city'
                ])
                ->first();
        }

        // TODO - 27.11.19 - Сделать проверку если филиал не найден, кидать на 404 или редиректить
        $this->filial = $site->filials->first();

        if (count($arr) > 2) {
            $site->load('filials.location.city');
        }
        $this->site = $site;
    }
}