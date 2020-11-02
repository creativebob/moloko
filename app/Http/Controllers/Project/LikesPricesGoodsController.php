<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LikesPricesGoodsController extends BaseController
{
    /**
     * LikesGoodsController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth_usersite');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        auth()->user()->likesGoods()->attach($request->prices_goods_id);
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        auth()->user()->likesGoods()->detach($id);
        return response()->json(['success' => true]);
    }
}
