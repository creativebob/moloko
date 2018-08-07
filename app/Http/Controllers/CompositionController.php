<?php

namespace App\Http\Controllers;

// Модели
use App\GoodsProduct;
use App\GoodsCategory;
use App\Goods;

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

    public function ajax_add(Request $request)
    {

        $composition = Raw::with(['raws_product.unit'])->findOrFail($request->id);

        return view($request->entity.'.compositions.composition-input', compact('composition'));
    }
    
    public function ajax_add_relation(Request $request)
    {

        $goods_category = GoodsCategory::findOrFail($request->goods_category_id);
        $goods_category->compositions()->attach($request->id);

        $composition = Raw::findOrFail($request->id);

        return view($request->entity.'.compositions.composition-input', compact('composition'));
    }

    public function ajax_delete_relation(Request $request)
    {

        $goods_category = GoodsCategory::findOrFail($request->goods_category_id);
        $res = $goods_category->compositions()->detach($request->id);

        if ($res) {
            $result = [
                'error_status' => 0,
            ];
        } else {
            $result = [
                'error_message' => 'Не удалось удалить состав!',
                'error_status' => 1,
            ];
        }
        
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
