<?php

namespace App\Http\Controllers;

// Модели
use App\Raw;

use Illuminate\Http\Request;

class CompositionController extends Controller
{
    // Сущность над которой производит операции контроллер
    protected $entity_name = 'compositions';
    protected $entity_dependence = false;

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

     // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_composition(Request $request)
    {
        $composition = Raw::with(['article.group.unit', 'category'])
        ->find($request->id);

        return view('goods.compositions.composition_input', compact('composition'));
    }

    // Добавляем состав
    public function ajax_get_category_composition(Request $request)
    {

        $composition = Raw::with(['article.group.unit', 'category'])
        ->findOrFail($request->id);

        return view('goods_categories.compositions.composition_tr', compact('composition'));
    }

    // Удаляем состав
    // public function ajax_delete_relation(Request $request)
    // {

    //     $goods_category = GoodsCategory::findOrFail($request->goods_category_id);
    //     $res = $goods_category->compositions()->detach($request->id);

    //     return response()->json(isset($res) ? true : 'Не удалось удалить состав!');
    // }
}
