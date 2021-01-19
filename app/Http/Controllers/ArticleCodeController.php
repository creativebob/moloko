<?php

namespace App\Http\Controllers;

use App\ArticleCode;
use Illuminate\Http\Request;

class ArticleCodeController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * ArticleCodeController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $articleCode = ArticleCode::create($data);
        return response()->json($articleCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $res = ArticleCode::destroy($id);
        return response()->json($res);
    }
}
