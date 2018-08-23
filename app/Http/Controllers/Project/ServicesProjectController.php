<?php

namespace App\Http\Controllers\Project;

// Модели
use App\Company;
use App\Navigation;
use App\Catalog;
use App\Service;

use App\Http\Controllers\Project\Traits\GeneralTrait;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServicesProjectController extends Controller
{
    protected $page_alias = 'services';
    use GeneralTrait;

    public function index()
    {


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

    public function show($id = null)
    {   

        $department = $this->department();
        // dd($department);

        $graphics = $this->graphics();
        // dd($graphics);

        $navigations = $this->navigations();
        // dd($navigations);

        if ($id == null) {
            $current_catalog = Catalog::with(['services' => function ($query) {
                $query->with('services_article')
                ->whereNull('archive')
                ->whereNull('draft')
                ->orderBy('sort', 'asc');
            }])
            ->where('parent_id', 1)
            ->first();
            $id = $current_catalog->id;
        } else {
            $current_catalog = Catalog::with(['services' => function ($query) {
                $query->with('services_article')
                ->whereNull('archive')
                ->whereNull('draft')
                ->orderBy('sort', 'asc');
            }])
            ->findOrFail($id);
        }

        // dd($current_catalog);
        
        
        $services = $current_catalog->services()
        ->wherePivot('display', 1)
        ->get();

        // dd($services);
        
        // dd($services);

        $catalogs = Catalog::where('company_id', 1)->get()->keyBy('id')->toArray();

        $catalogs_list = [];
        $item = null;

        if (isset($catalogs[$id]['parent_id'])) {
                // dd('lol');
            $catalogs[$catalogs[$id]['parent_id']]['item_id'] = $id;
            $item = $catalogs[$id]['parent_id'];
            

            for ($i = 0; $i < count($catalogs); $i++) {
                if (isset($catalogs[$item]['parent_id'])) {
                    $item;
                    $catalogs[$catalogs[$item]['parent_id']]['item_id'] = $id;
                    $item = $catalogs[$item]['parent_id'];

                }
            // dd($catalogs);
            }
        }
        
        // dd($catalogs);
        if ($catalogs) {

            $catalogs_tree = get_parents_tree_with_item_id($catalogs, $id);
        } else {
            $catalogs_tree = null;
        }
        // dd($catalogs_tree);
        
        // dd($id);
        // dd($services_category);
        // dd($services_category->description);

        $alias = $this->page_alias;

        return view('project.services.index', compact('department', 'graphics', 'alias', 'navigations', 'catalogs_tree', 'id', 'current_catalog', 'services'));
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
