<?php

namespace App\Http\Controllers\Project;


use App\Lead;

class OrderController extends BaseController
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
        $leads = Lead::with([
            'estimate.services_items' => function ($q) {
                $q->with([
                    'service.process.photo',
                    'flow'
                ]);
            }
        ])
            ->where('user_id', auth()->id())
            ->get();

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'orders')
            ->first();

        return view($site->alias.'.pages.orders.index', compact('site',  'page', 'leads'));
    }
}
