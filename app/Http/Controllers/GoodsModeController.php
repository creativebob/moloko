<?php

namespace App\Http\Controllers;

use App\GoodsMode;
use Illuminate\Http\Request;

class GoodsModeController extends Controller
{
    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param  \App\GoodsMode  $goodsMode
     * @return \Illuminate\Http\Response
     */
    public function show(GoodsMode $goodsMode)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\GoodsMode  $goodsMode
     * @return \Illuminate\Http\Response
     */
    public function edit(GoodsMode $goodsMode)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GoodsMode  $goodsMode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GoodsMode $goodsMode)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\GoodsMode  $goodsMode
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoodsMode $goodsMode)
    {
        //
    }
}
