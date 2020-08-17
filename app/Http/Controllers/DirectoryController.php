<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DirectoryController extends Controller
{

      // Сущность над которой производит операции контроллер
      protected $entity_name = 'directories';
      protected $entity_dependence = true;

    public function index()
    {


        // $disk = Storage::disk('local');

        // Storage::makeDirectory('public/companies/users/avatars');

        // $directories = Storage::directories('public');

        // // dd($directories);




        // Инфо о странице
        $pageInfo = pageInfo($this->entity_name);

        return view('directories.index', compact('pageInfo', 'directories'));

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
        //
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
