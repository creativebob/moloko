<?php

namespace App\Http\Controllers;

use App\Action;
use App\ActionEntity;
use App\AttachmentsStock;
use App\CatalogsGoods;
use App\CatalogsGoodsItem;
use App\Consignment;
use App\ContainersStock;
use App\EstimatesGoodsItem;
use App\GoodsStock;
use App\Menu;
use App\Observers\Traits\CategoriesTrait;
use App\Off;
use App\Page;
use App\Production;
use App\RawsStock;
use App\Right;
use DB;
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

    public function draft_process($alias, $id)
    {
        $entity = Entity::whereAlias($alias)->first(['model']);
        $model = 'App\\'.$entity->model;

        $item = $model::findOrFail($id);

        $item->process->update([
            'draft' => true
        ]);

        return redirect()->route($alias.'.edit', $id);
    }

    public function cache()
    {
        \Artisan::call('optimize');
        \Artisan::call('view:cache');

        return "Кэш установлен.";
    }

    public function cache_clear()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('modelCache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');
//        \Artisan::call('backup:clean');

        return "Кэш очищен.";
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

        Page::insert([
            [
                'name' => 'Домены',
                'site_id' => 1,
                'title' => 'Домены',
                'description' => 'Домены',
                'alias' => 'domains',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);

        echo 'Созданы страницы<br>';

//        Menu::insert([
//            //  Вложения
//            //  Инструменты
//            [
//                'name' => 'Инструменты',
//                'icon' => 'icon-tool',
//                'alias' => null,
//                'tag' => 'tools',
//                'parent_id' => null,
//                'page_id' => null,
//                'navigation_id' => 1,
//                'company_id' => null,
//                'system' => true,
//                'author_id' => 1,
//                'display' => true,
//                'sort' => 14,
//            ],
//
//            //  Помещения
//            [
//                'name' => 'Помещения',
//                'icon' => 'icon-room',
//                'alias' => null,
//                'tag' => 'rooms',
//                'parent_id' => null,
//                'page_id' => null,
//                'navigation_id' => 1,
//                'company_id' => null,
//                'system' => true,
//                'author_id' => 1,
//                'display' => true,
//                'sort' => 15,
//            ],
//
//        ]);
//
//        echo "Добавлены 2 главные категори меню<br>";


        $pages = Page::get();
        $menus = Menu::get();

//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'tools'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-tool')->first()->id,
//        ]);
//
//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'tools_categories'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-tool')->first()->id,
//        ]);

//        echo "Перенесены инструменты<br>";

        Menu::insert([
            [
                'name' => 'Домены',
                'icon' => null,
                'alias' => 'admin/domains',
                'tag' => 'domains',
                'parent_id' => $menus->where('tag', 'marketings')->first()->id,
                'page_id' => $pages->where('alias', 'domains')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
        ]);

        echo "Пункты меню<br>";

//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'rooms'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-room')->first()->id,
//        ]);
//
//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'rooms_categories'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-room')->first()->id,
//        ]);
//
//        $menu = Menu::where([
//            'navigation_id' => 1,
//            'tag' => 'stocks'
//        ])->first();
//        $menu->update([
//            'parent_id' => $menus->where('icon', 'icon-room')->first()->id,
//        ]);
//
//        echo "Перенесены комнаты и склады<br>";


        Entity::insert([
            [
                'name' => 'Домены',
                'alias' => 'domains',
                'model' => 'Domain',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'domains',
                'page_id' => $pages->firstWhere('alias', 'domains')->id,
            ],

        ]);

        echo 'Созданы сущности<br>';


        // Наваливание прав

        // Добавленным
        $entities = Entity::whereIn('alias', [
            'domains',
        ])
            ->get();
        // Всем
//        $entities = Entity::get();

        foreach($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach($actions as $action){
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach($actionentities as $actionentity){

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach($rights as $right){
                $mass[] = ['right_id' => $right->id, 'role_id' => 1, 'system' => 1];
            };

            DB::table('right_role')->insert($mass);

            $mass = null;
            $mass = [];
            foreach($rights as $right){
                $mass[] = ['right_id' => $right->id, 'role_id' => 2, 'system' => 1];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Навалены права<br>";
    }

    public function roll_house_parser()
    {
        $old_catalog_goods = CatalogsGoods::first();
        $catalog_goods = $old_catalog_goods->replicate();
        $catalog_goods->save();

        $old_catalogs_goods_items = CatalogsGoodsItem::whereNull('parent_id')
        ->get();

        foreach ($old_catalogs_goods_items as $old_item) {
            $item = $old_item->replicate();
            $item->catalogs_goods_id = $catalog_goods->id;
            $item->save();

            $old_item->load('prices');

            if ($old_item->prices) {
                foreach ($old_item->prices as $old_price) {
                    if ($old_price->filial_id == 2) {
                        $old_price->catalogs_goods_item_id = $item->id;
                        $old_price->catalogs_goods_id = $catalog_goods->id;
                        $old_price->save();
                    }
                }
            }

            $old_item->load('childs');

            if ($old_item->childs) {
                foreach ($old_item->childs as $old_child_1) {
                    $child_item_1 = $old_child_1->replicate();
                    $child_item_1->parent_id = $item->id;
                    $child_item_1->category_id = $item->id;
                    $child_item_1->catalogs_goods_id = $catalog_goods->id;
                    $child_item_1->save();

                    $old_child_1->load('prices');

                    if ($old_child_1->prices) {
                        foreach ($old_child_1->prices as $old_price) {
                            if ($old_price->filial_id == 2) {
                                $old_price->catalogs_goods_item_id = $child_item_1->id;
                                $old_price->catalogs_goods_id = $catalog_goods->id;
                                $old_price->save();
                            }
                        }
                    }

                    $old_child_1->load('childs');

                    if ($old_child_1->childs) {
                        foreach ($old_child_1->childs as $old_child_2) {
                            $child_item_2 = $old_child_2->replicate();
                            $child_item_2->parent_id = $child_item_1->id;
                            $child_item_2->category_id = $item->id;
                            $child_item_2->catalogs_goods_id = $catalog_goods->id;
                            $child_item_2->save();

                            $old_child_2->load('prices');

                            if ($old_child_2->prices) {
                                foreach ($old_child_2->prices as $old_price) {
                                    if ($old_price->filial_id == 2) {
                                        $old_price->catalogs_goods_item_id = $child_item_2->id;
                                        $old_price->catalogs_goods_id = $catalog_goods->id;
                                        $old_price->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        echo 'Гатова';

    }

}
