<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Project\Traits\Commonable;
use App\Tool;
use Illuminate\Http\Request;

class ToolController extends Controller
{

    use Commonable;

    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $site = $this->site;
//
//        $page = $site->pages_public
//            ->where('alias', 'tools')
//            ->first();
//
//        return view($site->alias.'.pages.tools.index', compact('site',  'page'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tool = Tool::with([
            'article'
        ])
            ->findOrFail($id);

        $site = $this->site;

        $page = $site->pages_public
            ->where('alias', 'tool')
            ->first();

        return view($site->alias.'.pages.tool.index', compact('site',  'page', 'tool'));
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
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
     * Удаление указанного ресурса из хранилища.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
