<?php

namespace App\Http\Controllers;

use App\Off;
use Illuminate\Http\Request;

class OffController extends Controller
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
     * @param  \App\Off  $off
     * @return \Illuminate\Http\Response
     */
    public function show(Off $off)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\Off  $off
     * @return \Illuminate\Http\Response
     */
    public function edit(Off $off)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Off  $off
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Off $off)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\Off  $off
     * @return \Illuminate\Http\Response
     */
    public function destroy(Off $off)
    {
        //
    }
}
