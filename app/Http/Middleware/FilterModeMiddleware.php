<?php

namespace App\Http\Middleware;

use Closure;

// Куки
use Illuminate\Support\Facades\Cookie;

class FilterModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    
    public function handle($request, Closure $next)
    {

            dd($request);
            if ($request->cookie('backlink') != null) {

                if($request->filter_mode == 'disable'){

                    Cookie::queue(Cookie::forget('backlink'));

                    $backlink = $request->Url();
                    return Redirect($backlink);

                    // return $next($request); 
                };

                $backlink = Cookie::get('backlink');
                Cookie::queue(Cookie::forget('backlink'));

                return Redirect($backlink);
            }

        return $next($request);
    }
}
