<?php

namespace App\Http\Controllers;

use App\AttachmentsStock;
use App\Consignment;
use App\ContainersStock;
use App\EstimatesGoodsItem;
use App\GoodsStock;
use App\Observers\Traits\CategoriesTrait;
use App\Off;
use App\Production;
use App\RawsStock;
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
        $raws_stocks = RawsStock::get();
        foreach ($raws_stocks as $raws_stock) {
            $raws_stock->free = ($raws_stock->count > 0) ? $raws_stock->count : 0;
            $raws_stock->save();
        }
        echo ('Сырье');

        $goods_stocks = GoodsStock::get();
        foreach ($goods_stocks as $goods_stock) {
            $goods_stock->free = ($goods_stock->count > 0) ? $goods_stock->count : 0;
            $goods_stock->save();
        }
        echo ('Товары');

        $containers_stocks = ContainersStock::get();
        foreach ($containers_stocks as $containers_stock) {
            $containers_stock->free = ($containers_stock->count > 0) ? $containers_stock->count : 0;
            $containers_stock->save();
        }
        echo ('Упаковки');

        $attachments_stocks = AttachmentsStock::get();
        foreach ($attachments_stocks as $attachments_stock) {
            $attachments_stock->free = ($attachments_stock->count > 0) ? $attachments_stock->count : 0;
            $attachments_stock->save();
        }
        echo ('Вложения');

    }

}
