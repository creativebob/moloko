<?php

namespace App\Http\Controllers;

use App\FiltersGoods;
use Illuminate\Http\Request;

class FiltersGoodsController extends Controller
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
     * @param  \App\FiltersGoods  $filtersGoods
     * @return \Illuminate\Http\Response
     */
    public function show(FiltersGoods $filtersGoods)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\FiltersGoods  $filtersGoods
     * @return \Illuminate\Http\Response
     */
    public function edit(FiltersGoods $filtersGoods)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FiltersGoods  $filtersGoods
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FiltersGoods $filtersGoods)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\FiltersGoods  $filtersGoods
     * @return \Illuminate\Http\Response
     */
    public function destroy(FiltersGoods $filtersGoods)
    {
        //
    }
}
