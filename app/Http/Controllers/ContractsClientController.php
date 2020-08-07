<?php

namespace App\Http\Controllers;

use App\ContractsClient;
use Illuminate\Http\Request;

class ContractsClientController extends Controller
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
     * @param  \App\ContractsClient  $contractsClient
     * @return \Illuminate\Http\Response
     */
    public function show(ContractsClient $contractsClient)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\ContractsClient  $contractsClient
     * @return \Illuminate\Http\Response
     */
    public function edit(ContractsClient $contractsClient)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContractsClient  $contractsClient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContractsClient $contractsClient)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\ContractsClient  $contractsClient
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContractsClient $contractsClient)
    {
        //
    }
}
