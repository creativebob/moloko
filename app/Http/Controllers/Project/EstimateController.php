<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use App\Models\Project\Estimate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EstimateController extends Controller
{

    use Commonable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $estimates = Estimate::with([
            'goods_items' => function ($q) {
                $q->with([
                    'goods.article.photo',
                    'price_goods' => function ($q) {
                        $q->with([
                            'currency',
                            'catalogs_item.directive_category:id,alias'
                        ]);
                    }
                ]);
            },
            'lead'
        ])
            ->whereHas('lead', function ($q) {
                $q->where('user_id', auth()->user()->id);
            })
            ->get()->sortByDesc('id');
//        dd($estimates);

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'estimates')
            ->first();

        return view($site->alias.'.pages.estimates.index', compact('site',  'page', 'estimates'));
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

	    $estimate = Estimate::with('lead')
		    ->whereHas('lead', function($q){
			    $q->where('user_id', Auth::user()->id);
		    })
		    ->findOrFail($id);

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'estimates-items')
            ->first();

        return view($site->alias.'.pages.estimates_items.index', compact('site',  'page', 'estimate'));
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
