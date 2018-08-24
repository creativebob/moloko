<?php

namespace App\Http\Controllers\Project;

// Модели
use App\Company;
use App\Navigation;

use App\Staffer;

use App\Http\Controllers\Project\Traits\GeneralTrait;

use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AboutProjectController extends Controller
{
    protected $page_alias = 'about';
    use GeneralTrait;

    public function index()
    {

        // dd('lol');
        $department = $this->department();
        // dd($department);

        $graphics = $this->graphics();
        // dd($graphics);

        $navigations = $this->navigations();
        // dd($navigations);

        $staff = Staffer::with('user.avatar')->orderBy('sort', 'asc')->find([1, 2]);
        // dd($staff);
        
        $alias = $this->page_alias;

        return view('project.about.index', compact('department', 'graphics', 'alias', 'navigations', 'staff'));
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
