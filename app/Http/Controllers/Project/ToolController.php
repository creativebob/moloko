<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Project\Traits\Commonable;
use App\Tool;
use Illuminate\Http\Request;

class ToolController extends BaseController
{
    /**
     * ToolController constructor.
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
//        $site = $this->site;
//
//        $page = $site->pages_public
//            ->where('alias', 'tools')
//            ->first();
//
//        return view($site->alias.'.pages.tools.index', compact('site',  'page'));
    }

    /**
     * Display the specified resource.
     * 
     * @param $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($slug)
    {
        $tool = Tool::with([
            'article'
        ])
            ->whereHas('article', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->first();
//        dd($tool);
        if (empty($tool)) {
            abort(404);
        }

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'equipment')
            ->first();

        return view($site->alias . '.pages.equipment.index', compact('site', 'page', 'tool'));
    }
}
