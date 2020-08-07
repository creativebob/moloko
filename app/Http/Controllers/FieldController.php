<?php

namespace App\Http\Controllers;

// Модели
use App\Entity;


use Illuminate\Support\Facades\Schema;

use Illuminate\Http\Request;

class FieldController extends Controller
{
    // Сущность над которой производит операции контроллер
    protected $entity_name = 'fields';
    protected $entity_dependence = false;

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

    public function  ajax_fields_list(Request $request)
    {
        $entity = Entity::findOrFail($request->entity_id);
         // $entity = Entity::findOrFail(7);
        $fields = Schema::getColumnListing($entity->alias);
        $fields_list = [];
        foreach ($fields as $field) {
            $fields_list[$field] = $field;
        }

        return view('stages.fields_list', compact('fields_list'));
    }

}
