<?php

namespace App\Http\Controllers;

use App\CostsHistory;
use Illuminate\Http\Request;

class CostsHistoryController extends Controller
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
     * @param  \App\CostsHistory  $costsHistory
     * @return \Illuminate\Http\Response
     */
    public function show(CostsHistory $costsHistory)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\CostsHistory  $costsHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(CostsHistory $costsHistory)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CostsHistory  $costsHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CostsHistory $costsHistory)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\CostsHistory  $costsHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(CostsHistory $costsHistory)
    {
        //
    }
}
