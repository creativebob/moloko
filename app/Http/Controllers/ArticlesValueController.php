<?php

namespace App\Http\Controllers;

use App\ArticlesValue;
use Illuminate\Http\Request;

class ArticlesValueController extends Controller
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
     * @param  \App\ArticlesValue  $articlesValue
     * @return \Illuminate\Http\Response
     */
    public function show(ArticlesValue $articlesValue)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\ArticlesValue  $articlesValue
     * @return \Illuminate\Http\Response
     */
    public function edit(ArticlesValue $articlesValue)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ArticlesValue  $articlesValue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ArticlesValue $articlesValue)
    {
        //
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\ArticlesValue  $articlesValue
     * @return \Illuminate\Http\Response
     */
    public function destroy(ArticlesValue $articlesValue)
    {
        //
    }
}
