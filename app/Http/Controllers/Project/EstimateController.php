<?php

namespace App\Http\Controllers\Project;

use App\Models\Project\Estimate;

class EstimateController extends BaseController
{
    /**
     * EstimateController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth_usersite');
    }

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
                            'catalogs_item.directive_category:id,alias',
                            'catalog',
                        ]);
                    }
                ]);
            },
            'lead'
        ])
            ->whereHas('lead', function ($q) {
                $q->where('user_id', auth()->user()->id);
            })
            ->where('is_dismissed', false)
            ->orderBy('id', 'desc')
            ->get();
//        dd($estimates);

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'estimates')
            ->first();

        return view($site->alias.'.pages.estimates.index', compact('site',  'page', 'estimates'));
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
			    $q->where('user_id', auth()->user()->id);
		    })
		    ->find($id);

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'estimates-items')
            ->first();

        return view($site->alias.'.pages.estimates_items.index', compact('site',  'page', 'estimate'));
    }
}
