<?php

namespace App\Http\Controllers;

use App\Consignment;
use App\Observers\Traits\CategoriesTrait;
use App\Production;
use Illuminate\Http\Request;

use App\Entity;

class AppController extends Controller
{
    use CategoriesTrait;

    // Вход в crm
    public function enter()
    {
        return view('layouts.enter');
    }

    public function recalculate_categories($entity_alias)
    {
        $entity = Entity::whereAlias($entity_alias)
            ->first([
                'model'
            ]);
        $model = 'App\\'.$entity->model;

        $categories = $model::whereNull('parent_id')
        ->get();

        $this->recalculateCategories($categories);

        return redirect()->route($entity_alias.'.index');
    }

    public function draft_article($alias, $id)
    {
        $entity = Entity::whereAlias($alias)->first(['model']);
        $model = 'App\\'.$entity->model;

        $item = $model::findOrFail($id);

        $item->article->update([
            'draft' => true
        ]);

        return redirect()->route($alias.'.edit', $id);
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    // Сортировка
    public function ajax_sort(Request $request, $entity_alias)
    {

    	$entity = Entity::whereAlias($entity_alias)->first(['model']);
    	$model = 'App\\'.$entity->model;

        $i = 1;
        foreach ($request->$entity_alias as $item) {
            $model::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system(Request $request)
    {

    	$entity = Entity::whereAlias($request->entity_alias)->first(['model']);
    	$model = 'App\\'.$entity->model;

        // $item = $model::findOrFail($request->id);
        // $item->system = ($request->action == 'lock') ? 1 : null;
        // $item->save();

        // if (isset($request->entity)) {
        //     # code...
        // } else {
        //     if ($request->type == 'menu') {
        //         return view('')
        //     } else {

        //     }
        // }

        $item = $model::where('id', $request->id)->update(['system' => ($request->action == 'lock') ? true : false]);

        return response()->json(isset($item) ?? 'Ошибка при обновлении статуса системной записи!');
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

    	$entity = Entity::whereAlias($request->entity_alias)->first(['model']);
    	$model = 'App\\'.$entity->model;
        $item = $model::where('id', $request->id)->update(['display' => ($request->action == 'show') ? true : false]);

        return response()->json(isset($item) ?? 'Ошибка при обновлении отображения на сайте!');
    }

    // Сортировка
    public function ajax_check(Request $request)
    {

        $entity = Entity::whereAlias($request->entity_alias)->first(['model']);
        $model = 'App\\'.$entity->model;

        $id = $request->id;

        // Проверка поля в нашей базе данных
        $result_count = $model::where($request->field, $request->value)
            ->where('id', '!=', $request->id)
//            ->when($id, function ($q, $id) {
//                return $q->where('id', '!=', $id);
//            })
            ->whereCompany_id($request->user()->company_id)
            ->count();

        return response()->json($result_count);
    }

    public function parser()
    {
        $consignments = Consignment::with([
            'items.cmv.article'
        ])
            ->get();

//        dd($consignments->first());
        foreach ($consignments as $consignment) {
            foreach($consignment->items as $item) {
                if (empty($item->manufacturer_id)) {
                    if(isset($item->cmv->article->manufacturer_id)) {
                        $item->update([
                            'manufacturer_id' => $item->cmv->article->manufacturer_id
                        ]);
                    }
                }
            }
        }

        echo('Отпарсил товарные накладные');

        $productions = Production::with([
            'items.cmv.article'
        ])
            ->get();

//        dd($productions->first());
        foreach ($productions as $production) {
            foreach($production->items as $item) {
                if (empty($item->manufacturer_id)) {
                    if(isset($item->cmv->article->manufacturer_id)) {
                        $item->update([
                            'manufacturer_id' => $item->cmv->article->manufacturer_id
                        ]);
                    }
                }
            }
        }

        echo('Отпарсил наряды на производство');
    }
}
