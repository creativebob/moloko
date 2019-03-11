<?php

namespace App\Http\Controllers\Project;

// Модель
use App\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactsController extends IndexProjectController
{

    public function __construct(Request $request)
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $city)
    {

        $site = Site::with(['departments.location.city', 'departments.schedules.worktimes', 'pages' => function ($query) {
            $query->where('display', 1);
        }, 'navigations' => function ($query) {
            $query->with(['navigations_category', 'menus' => function ($query) {
                $query->with('page')->where('display', 1);
            }]);
        }])->findOrFail(2);

        $alias = 'contacts';


        $departments_worktimes = $site->departments->keyBy('location.city.alias');

            // dd($departments_worktimes[$city]);

        $department_worktime = [];
        foreach ($departments_worktimes[$city]->schedules[0]->worktimes as $worktime) {
                // dd($worktime);
            $department_worktime[$worktime->weekday]['worktime_begin'] = secToTime($worktime->worktime_begin);
            $department_worktime[$worktime->weekday]['worktime_end'] = secToTime($worktime->worktime_begin + $worktime->worktime_interval);
        }
            // dd($department_worktime);

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

        // Проверяем на существование города
        if (!in_array($city, $cities)) {

         $city = $cities[0];
         $error_message = 'Такого филиала не существует...';

         return view('project.errors.404', compact('error_message', 'alias', 'city', 'navigations', 'departments'));

            // abort(404, 'Такого филиала не существует...');
            // $city = $cities[0];
            // return redirect()->action('IndexController@index', ['city' => $city, 'alias' => $alias]);
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

    return view('project.contacts.index', compact('alias', 'page', 'city', 'navigations', 'departments', 'content', 'alias', 'department_worktime'));

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
