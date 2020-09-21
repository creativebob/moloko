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
     * Show the form for creating a new resource.
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
        $entity = Entity::find($request->entity_id);
         // $entity = Entity::find(7);
        $fields = Schema::getColumnListing($entity->alias);
        $fields_list = [];
        foreach ($fields as $field) {
            $fields_list[$field] = $field;
        }

        return view('stages.fields_list', compact('fields_list'));
    }

}
