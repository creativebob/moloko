<?php

namespace App\Http\Controllers;

use App\TemplatesCategory;
use Illuminate\Http\Request;

class TemplatesCategoryController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * TemplatesCategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'templates_categories';
        $this->entityDependence = false;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\TemplatesCategory  $templatesCategory
     * @return \Illuminate\Http\Response
     */
    public function show(TemplatesCategory $templatesCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TemplatesCategory  $templatesCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(TemplatesCategory $templatesCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TemplatesCategory  $templatesCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TemplatesCategory $templatesCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TemplatesCategory  $templatesCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(TemplatesCategory $templatesCategory)
    {
        //
    }
}
