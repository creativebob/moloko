<?php

namespace App\Http\Middleware;

use App\Page;
use Illuminate\Support\Facades\Auth;

use Closure;

class GenerateMenus
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

        $mass =[];
        $pages = Page::whereSite_id(1)
                        // ->orWhere('site_id', 1)
                        ->get();
        foreach ($pages as $page) {
            $mass = [$page->page_name, $page->page_alias];
        };
        // dd($mass);
        \Menu::make('Sidebar', function ($menu) {
            
                $menu->add('Lol', '/lol');
            
            

        });
        return $next($request);
    }
}
