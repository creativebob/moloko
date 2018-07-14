<?php

namespace App\Http\Controllers;

// Модели
use App\Site;

use Illuminate\Http\Request;


class ApiController extends Controller
{
     // Получаем сайт по api
    public function api_index (Request $request)
    {
        $site = Site::where('api_token', $request->token)->first();

        if ($site) {
            // return Cache::remember('site', 1, function() use ($domain) {
            return Site::with(['departments.location.city',
            	'company.departments.staff' => function ($query) {
            		$query->with(['schedules.worktimes', 'user']);
            	},
                'company.location.city',
                'company.services_categories' => function ($query) {
                    $query->with(['services_products' => function ($query) {
                        $query->with('services')->whereDisplay(1);
                    }])->whereDisplay(1);
                }, 'company.services_products' => function ($query) {
                        $query->with('services')->whereDisplay(1);
                }, 'pages' => function ($query) {
                    $query->whereDisplay(1);
                }, 'navigations.menus.page',
                'navigations.navigations_category',
                'navigations' => function ($query) {
                    $query->whereDisplay(1);
                }, 'navigations.menus' => function ($query) {
                    $query->whereDisplay(1)->orderBy('sort', 'asc');
                }])->whereDomain($request->domain)
            ->orderBy('sort', 'asc')
            ->first();
            // });
        } else {
            return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
        }
    }
     
     // -------------------------------------------- API -----------------------------------------------
    // Получаем сайт по api
    public function api(Request $request, $alias)
    {

        $site = Site::where('api_token', $request->token)->first();

        if ($site) {
            // return Cache::forever($domen.'-news', $site, function() use ($city, $token) {
            $page = null;

            return $page;
            // });
        } else {
            return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
        }  

        return null;
    }
    
    
     // Конкретная категория
    public function api_show(Request $request, $id)
    {

// dd('lol');
        $site = Site::where('api_token', $request->token)->first();
        if ($site) {
            // return Cache::remember('staff', 1, function() use ($domen) {
            $services_category = ServicesCategory::with(['photo', 'services_products' => function ($query) {
                $query->with([ 'services' => function ($query) {
                    $query->where('display', 1);
                }])->where('display', 1);
            }])->findOrFail($id);
            return $services_category;
            // });
        } else {
            return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
        }
    }
    
    public function index(Request $request)
    {
    	switch ($request->alias) {
      case 'medcosm':
      	
      	switch ($request->action) {
      		case 'boot':
	        return Site::with(['company.departments.staff' => function ($query) {
	            		$query->with(['schedules.worktimes', 'user']);
	            	},
	                'company.location.city',
	                'company.services_categories' => function ($query) {
	                    $query->with(['services_products' => function ($query) {
	                        $query->with('services')->whereDisplay(1);
	                    }])->whereDisplay(1);
	                }, 'company.services_products' => function ($query) {
	                        $query->with('services')->whereDisplay(1);
	                }, 'pages' => function ($query) {
	                    $query->whereDisplay(1);
	                }, 'navigations.menus.page',
	                'navigations.navigations_category',
	                'navigations' => function ($query) {
	                    $query->whereDisplay(1);
	                }, 'navigations.menus' => function ($query) {
	                    $query->whereDisplay(1)->orderBy('sort', 'asc');
	                }])->whereDomain($request->domain)
	            ->orderBy('sort', 'asc')
	            ->first();
	        break;
	      
	      case ('page'):

            $page = null;

            return $page;


        return null;
	        # code...
	        break;
    	}
      	
      	
        return redirect()->action('ApiController@medcosm', ['action' => $request->action]);
        break;
      
      default:
        # code...
        break;
    }
    }
    
    public function medcosm(Request $request)
    {
    	switch ($request->action) {
      		case 'boot':
	        return Site::with(['departments.location.city',
	            	'company.departments.staff' => function ($query) {
	            		$query->with(['schedules.worktimes', 'user']);
	            	},
	                'company.location.city',
	                'company.services_categories' => function ($query) {
	                    $query->with(['services_products' => function ($query) {
	                        $query->with('services')->whereDisplay(1);
	                    }])->whereDisplay(1);
	                }, 'company.services_products' => function ($query) {
	                        $query->with('services')->whereDisplay(1);
	                }, 'pages' => function ($query) {
	                    $query->whereDisplay(1);
	                }, 'navigations.menus.page',
	                'navigations.navigations_category',
	                'navigations' => function ($query) {
	                    $query->whereDisplay(1);
	                }, 'navigations.menus' => function ($query) {
	                    $query->whereDisplay(1)->orderBy('sort', 'asc');
	                }])->whereDomain($request->domain)
	            ->orderBy('sort', 'asc')
	            ->first();
	        break;
	      
	      default:
	        # code...
	        break;
    	}
    	
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
