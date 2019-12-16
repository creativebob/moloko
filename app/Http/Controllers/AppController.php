<?php

namespace App\Http\Controllers;

use App\Action;
use App\ActionEntity;
use App\AttachmentsStock;
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

    public function cahche()
    {
        \Artisan::call('optimize');
        \Artisan::call('view:cache');

        return "Кэш установлен.";
    }

    public function cahche_clear()
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
            // Вложения
            [
                'name' => 'Склады инструментов',
                'site_id' => 1,
                'title' => 'Склады инструментов',
                'description' => 'Склады инструментов',
                'alias' => 'tools_stocks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);

        echo "Создана страница Склады инструментов<br>";

        Menu::insert([
            //  Вложения
            //  Инструменты
            [
                'name' => 'Инструменты',
                'icon' => 'icon-tool',
                'alias' => null,
                'tag' => 'tools',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 14,
            ],

            //  Помещения
            [
                'name' => 'Помещения',
                'icon' => 'icon-room',
                'alias' => null,
                'tag' => 'rooms',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 15,
            ],

        ]);

        echo "Добавлены 2 главные категори меню<br>";


        $pages = Page::get();
        $menus = Menu::get();

        $menu = Menu::where([
            'navigation_id' => 1,
            'tag' => 'tools'
        ])->first();
        $menu->update([
            'parent_id' => $menus->where('icon', 'icon-tool')->first()->id,
        ]);

        $menu = Menu::where([
            'navigation_id' => 1,
            'tag' => 'tools_categories'
        ])->first();
        $menu->update([
            'parent_id' => $menus->where('icon', 'icon-tool')->first()->id,
        ]);

        echo "Перенесены инструменты<br>";

        Menu::insert([
            [
                'name' => 'Склады инструментов',
                'icon' => null,
                'alias' => 'admin/tools_stocks',
                'tag' => 'tools_stocks',
                'parent_id' => $menus->where('icon', 'icon-tool')->first()->id,
                'page_id' => $pages->where('alias', 'tools_stocks')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 1,
            ],
        ]);

        echo "Добавлены слады инструментов<br>";

        $menu = Menu::where([
            'navigation_id' => 1,
            'tag' => 'rooms'
        ])->first();
        $menu->update([
            'parent_id' => $menus->where('icon', 'icon-room')->first()->id,
        ]);

        $menu = Menu::where([
            'navigation_id' => 1,
            'tag' => 'rooms_categories'
        ])->first();
        $menu->update([
            'parent_id' => $menus->where('icon', 'icon-room')->first()->id,
        ]);

        $menu = Menu::where([
            'navigation_id' => 1,
            'tag' => 'stocks'
        ])->first();
        $menu->update([
            'parent_id' => $menus->where('icon', 'icon-room')->first()->id,
        ]);

        echo "Перенесены комнаты и склады<br>";


        Entity::insert([
            [
                'name' => 'Склад инструментов',
                'alias' => 'tools_stocks',
                'model' => 'ToolsStock',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('tools')->first(['id'])->id,
                'view_path' => 'attachments_stocks',
                'page_id' => $pages->firstWhere('alias', 'tools_stocks')->id,
            ],
        ]);

        echo "СОздана сущность складов инструментов<br>";


        // Наваливание прав

        // Добавленным
        $entities = Entity::whereIn('alias', [
            'tools_stocks',
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

}
