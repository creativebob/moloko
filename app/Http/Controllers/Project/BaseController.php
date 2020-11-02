<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Site;
use Illuminate\Support\Facades\Cookie;

class BaseController extends Controller
{
    /**
     * Сайт
     *
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    protected $site;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $request = request();
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
            if (isset($request->utm_source) ||
                isset($request->utm_term) ||
                isset($request->utm_content) ||
                isset($request->utm_campaign) ||
                isset($request->utm_medium)
            ) {

//                $array = [
//                    'utm_source',
//                    'utm_term',
//                    'utm_content',
//                    'utm_campaign',
//                    'utm_medium',
//                ];

                Cookie::queue(Cookie::forget('utm_source'));
                Cookie::queue(Cookie::forget('utm_term'));
                Cookie::queue(Cookie::forget('utm_content'));
                Cookie::queue(Cookie::forget('utm_campaign'));
                Cookie::queue(Cookie::forget('utm_medium'));

                if (isset($request->utm_source)) {
                    Cookie::queue(Cookie::forever('utm_source', $request->utm_source));
                }
                if (isset($request->utm_term)) {
                    Cookie::queue(Cookie::forever('utm_term', $request->utm_term));
                }
                if (isset($request->utm_content)) {
                    Cookie::queue(Cookie::forever('utm_content', $request->utm_content));
                }
                if (isset($request->utm_campaign)) {
                    Cookie::queue(Cookie::forever('utm_campaign', $request->utm_campaign));
                }
                if (isset($request->utm_medium)) {
                    Cookie::queue(Cookie::forever('utm_medium', $request->utm_medium));
                }
            }

            if (isset($request->prom)) {
                Cookie::queue('prom', json_encode($request->prom), 60 * 60 * 24);
//                return response()->cookie('prom', json_encode($request->prom), 60*60*24);
            }
        } else {
            abort(404, 'Сайт не существует');
        }
    }
}
