<?php

namespace App\Http\Controllers\Project;

// Модели
use App\Company;
use App\Navigation;
use App\Catalog;
use App\Album;

use App\Http\Controllers\Project\Traits\GeneralTrait;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CabinetProjectController extends Controller
{

    protected $page_alias = 'cabinet';
    use GeneralTrait;

    public function index()
    {

        $department = $this->department();
        // dd($department);
        
        $graphics = $this->graphics();
        // dd($graphics);

        $navigations = $this->navigations();
        // dd($navigations);
        // dd('lol');

        
        $album = Album::with(['photos' => function ($query) {
        	$query->whereDisplay(1)->orderBy('sort', 'asc');
        }])->whereDisplay(1)->whereAlias('slider')->first();
        // dd($album);

        // $photos = Photo::with('album')->whereHas('album', function ($query) {
        //     $query->where('alias', 'slider');
        // })->where('display', 1)->get();
        // dd($photos);

        $catalogs = Catalog::where('company_id', 1)->get();

        if ($catalogs) {
            $catalogs_list = [];
            foreach ($catalogs->toArray() as $catalog) {
                $catalogs_list[$catalog['id']] = $catalog;
            }
            $catalogs_tree = get_parents_tree($catalogs_list);
        } else {
            $catalogs_tree = null;
        }
        // dd($catalogs_tree);

        $alias = $this->page_alias;

        return view('project.cabinet.index', compact('department', 'graphics', 'alias', 'navigations', 'catalogs_tree', 'album'));
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
