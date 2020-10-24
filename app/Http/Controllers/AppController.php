<?php

namespace App\Http\Controllers;

use App\Action;
use App\ActionEntity;
use App\CatalogsGoods;
use App\CatalogsGoodsItem;
use App\GoodsCategory;
use App\Menu;
use App\Page;
use App\Right;
use App\Observers\System\Traits\Categorable;
use App\Entity;
use DB;
use Illuminate\Http\Request;


class AppController extends Controller
{
    /**
     * AppController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    use Categorable;

    public function resaveCategoriesGroups()
    {

        $goodsCategories = GoodsCategory::with([
            'goods.article'
        ])
        ->get();

        $count = 0;
        foreach ($goodsCategories as $goodsCategory) {
            $goodsCategory->groups()->detach();

            $insert = [];
            foreach($goodsCategory->goods as $curGoods) {
                $insert[] = $curGoods->article->articles_group_id;
            }

            $goodsCategory->groups()->attach($insert);
            $count++;
        }
        return "Перепривязаны группы артикулов у {$count} категорий";

    }

    /**
     * Перерасчет уровней и слагов для категорий выбранной сущности
     *
     * @param $entity_alias
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recalculate_categories($entity_alias)
    {
        $entity = Entity::whereAlias($entity_alias)
            ->first([
                'model'
            ]);
        $model = $entity->model;

        $categories = $model::whereNull('parent_id')
        ->get();

        $this->recalculateCategories($categories);

        return redirect()->route($entity_alias.'.index');
    }

    /**
     * Перевод в черновик артикула
     *
     * @param $alias
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function draft_article($alias, $id)
    {
        $entity = Entity::whereAlias($alias)->first(['model']);
        $model = $entity->model;

        $item = $model::find($id);

        $item->article->update([
            'draft' => true
        ]);

        return redirect()->route($alias.'.edit', $id);
    }

    /**
     * Перевод в черновик процесса
     *
     * @param $alias
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function draft_process($alias, $id)
    {
        $entity = Entity::whereAlias($alias)->first(['model']);
        $model = $entity->model;

        $item = $model::find($id);

        $item->process->update([
            'draft' => true
        ]);

        return redirect()->route($alias.'.edit', $id);
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    /**
     * Сортиовка
     *
     * @param Request $request
     * @param $entity_alias
     */
    public function ajax_sort(Request $request, $entity_alias)
    {

    	$entity = Entity::whereAlias($entity_alias)->first(['model']);
    	$model = $entity->model;

        $i = 1;
        foreach ($request->$entity_alias as $item) {
            $model::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    /**
     * Системная запись
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax_system(Request $request)
    {

        $entity_model = Entity::whereAlias($request->entity_alias)
            ->value('model');
        $model = $entity_model;

        // $item = $model::find($request->id);
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

        $item = $model::where('id', $request->id)
            ->update([
                'system' => $request->action
            ]);

        return response()->json(isset($item) ?? 'Ошибка при обновлении статуса системной записи!');
    }

    /**
     * Отображение на сайте
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax_display(Request $request)
    {
        $entity_model = Entity::whereAlias($request->entity_alias)
            ->value('model');
    	$model = $entity_model;

        $item = $model::where('id', $request->id)
            ->update([
                'display' => $request->action
            ]);

        return response()->json(isset($item) ?? 'Ошибка при обновлении отображения на сайте!');
    }

    /**
     * Проверка на совпадение имени
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax_check(Request $request)
    {

        $entity = Entity::whereAlias($request->entity_alias)->first(['model']);
        $model = $entity->model;

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
}
