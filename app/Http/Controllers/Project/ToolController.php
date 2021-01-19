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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tool = Tool::with([
            'article'
        ])
            ->find($id);
//        dd($tool);
        if (empty($tool)) {
            abort(404);
        }

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'tool')
            ->first();

        return view($site->alias.'.pages.tool.index', compact('site',  'page', 'tool'));
    }
}
