<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Project\Traits\Commonable;
use App\Promotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PromotionController extends BaseController
{
    /**
     * PromotionController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $site = $this->site;
        $promotions = Promotion::with('photo')
            ->whereHas('filials', function ($q) use ($site) {
                $q->where('id', $site->filial->id);
            })
            ->where('display', true)
            ->where('begin_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
//        dd($promotions);

        $page = $site->pages_public
            ->where('alias', 'promotions')
            ->first();

        return view($site->alias.'.pages.promotions.index', compact('site',  'page', 'promotions'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

	    $promotion = Promotion::with('photo')
		    ->find($id);

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'promotion')
            ->first();

        return view($site->alias.'.pages.promotion.index', compact('site',  'page', 'promotion'));
    }
}
