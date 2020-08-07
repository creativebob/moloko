<?php

namespace App\Http\Controllers;

use App\RawsMode;
use Illuminate\Http\Request;

class RawsModeController extends Controller
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
     * @param  \App\RawsMode  $rawsMode
     * @return \Illuminate\Http\Response
     */
    public function show(RawsMode $rawsMode)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\RawsMode  $rawsMode
     * @return \Illuminate\Http\Response
     */
    public function edit(RawsMode $rawsMode)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RawsMode  $rawsMode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RawsMode $rawsMode)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\RawsMode  $rawsMode
     * @return \Illuminate\Http\Response
     */
    public function destroy(RawsMode $rawsMode)
    {
        //
    }
}
