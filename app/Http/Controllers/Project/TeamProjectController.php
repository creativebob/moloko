<?php

namespace App\Http\Controllers\Project;

// Модель
use App\Staffer;
use App\Site;

// Кеш
use Illuminate\Support\Facades\Cache;

// Карбон (дата и время)
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Куки, для контроля форм
use Illuminate\Support\Facades\Cookie;

class TeamProjectController extends Controller
{

    public function index(Request $request)
    {

        // dd($city);

        $content = Staffer::with('user', 'position')
        ->whereHas('filial.location.city', function ($query) {
            $query->where('id', Cookie::get('city_id'));
        })
        ->where('company_id', 1)
        ->where('display', 1)
        ->whereNotNull('user_id')
        ->whereNull('moderation')
        ->orderBy('sort')
        ->get();

        // dd($content);

        $alias = 'team';

        // $site = Cache::rememberForever('vorotamars', function() {
            $site = Site::with(['departments.location.city', 'pages' => function ($query) {
                $query->where('display', 1);
            }, 'navigations' => function ($query) {
                $query->with(['navigations_category', 'menus' => function ($query) {
                    $query->with('page')->where('display', 1);
                }]);
            }])->findOrFail(2);
        //     return $site;
        // });
        // dd($request);


        $page = $site->pages->where('alias', $alias)->first();

        $navigations = $site->navigations->keyBy('navigations_category.tag');

        $departments = $site->departments->keyBy('location.city.alias');
        // dd($departments);

        $cities = [];
        foreach ($site->departments as $department) {
            $cities[] = $department->location->city->alias;
        }

        // dd($cities);
        // dd($city);
        // dd($content);

        if (isset($request->utm_source)) {
            Cookie::queue('utm-source', $request->utm_source, 135000);
        }
        if (isset($request->utm_term)) {
            Cookie::queue('utm-term', $request->utm_term, 135000);
        }
        if (isset($request->utm_content)) {
            Cookie::queue('utm-content', $request->utm_content, 135000);
        }
        if (isset($request->utm_campaign)) {
            Cookie::queue('utm-campaign ', $request->utm_campaign, 135000);
        }
        if (isset($request->utm_term)) {
            Cookie::queue('utm-medium ', $request->utm_medium, 135000);
        }

        // $content = Cache::rememberForever($alias, function() use ($city, $alias) {
        //     return json_decode(file_get_contents(env('CRM_DOMAIN').'/api/'.$city.'/'.$alias.'?token='.env('API_TOKEN')), true);
        // });

        // file_get_contents($site->domain.'/api/delete_cache/'.$alias.'?token='.$site->token), true);
        // dd($content);

        // Проверяем на существоввание страницы
        if ($page == null) {
            abort(404, 'Такой страницы не существует...');
        }

        return view('project.team.index', compact('alias', 'page', 'city', 'navigations', 'departments', 'content', 'alias', 'department_worktime'));
    }

    public function feedback(Request $request, $city)
    {

        $staffer = Staffer::with('user', 'position')->findOrFail($request->id);

        return view('project.team.modal', compact('staffer', 'city'));
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
