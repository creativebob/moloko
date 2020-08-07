<?php

namespace App\Http\Controllers;

use App\ServicesInfo;
use Illuminate\Http\Request;

class ServicesInfoController extends Controller
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
     * @param  \App\ServicesInfo  $servicesInfo
     * @return \Illuminate\Http\Response
     */
    public function show(ServicesInfo $servicesInfo)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\ServicesInfo  $servicesInfo
     * @return \Illuminate\Http\Response
     */
    public function edit(ServicesInfo $servicesInfo)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ServicesInfo  $servicesInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServicesInfo $servicesInfo)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\ServicesInfo  $servicesInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServicesInfo $servicesInfo)
    {
        //
    }
}
