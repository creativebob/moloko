<?php

namespace App\Http\Controllers\Project;


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
        $user = auth()->user();
        $leads = $user->userLeads;

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'orders')
            ->first();

        return view($site->alias.'.pages.orders.index', compact('site',  'page', 'leads'));
    }
}
