<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\ProductionsItemStoreRequest;
use App\Http\Requests\System\ProductionsItemUpdateRequest;
use App\ProductionsItem;
use Illuminate\Http\Request;

class ProductionsItemController extends Controller
{

    // Настройки сконтроллера
    public function __construct()
    {
        $this->middleware('auth');
    }

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
    public function store(ProductionsItemStoreRequest $request)
    {
        $data = $request->input();
        $production_item = ProductionsItem::create($data);

        $production_item->load([
            'cmv.article.unit',
            'entity:id,name,alias'
        ]);

        return response()->json($production_item);
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param  \App\ProductionsItem  $productionsItem
     * @return \Illuminate\Http\Response
     */
    public function show(ProductionsItem $productionsItem)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  \App\ProductionsItem  $productionsItem
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductionsItem $productionsItem)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductionsItem  $productionsItem
     * @return \Illuminate\Http\Response
     */
    public function update(ProductionsItemUpdateRequest $request, $id)
    {
        $production_item = ProductionsItem::findOrFail($id);

        $data = $request->input();
        $production_item->update($data);

        $production_item->load([
            'cmv.article.unit',
            'entity:id,name,alias'
        ]);

        return response()->json($production_item);
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  \App\ProductionsItem  $productionsItem
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = ProductionsItem::destroy($id);
        return response()->json($result);
    }
}
