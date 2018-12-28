<?php

use Illuminate\Database\Seeder;

// Модели
use App\Estimate;
use App\Navigation;
use App\Menu;
use App\Page;
use App\Entity;
use App\EntityPage;
use App\Action;
use App\ActionEntity;
use App\Right;

class IndicatorsTableSeeder extends Seeder
{

    public function run()
    {

        $page = Page::firstOrCreate(
            [
                'alias' => 'indicators',
                'site_id' => 1,
                'company_id' => null,
                'display' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Показатели',
                'title' => 'Показатели',
                'description' => 'Показатели',

            ]
        );

        $page_id = $page->id;

        // $entity = Entity::firstOrCreate(
        //     [
        //         'name' => 'Заказы',
        //         'alias' => 'orders',
        //         'model' => 'Order',
        //         'rights_minus' => null,
        //         'system_item' => 1,
        //         'author_id' => 1,
        //     ]
        // );

        $entity = Entity::where('alias', 'indicators')->first();
        if ($entity) {
            $entity_id = $entity->id;
        } else {
            $entity = new Entity;
            $entity->name = 'Показатели';
            $entity->alias = 'indicators';
            $entity->model = 'Шndicator';
            $entity->rights_minus = null;
            $entity->system_item = 1;
            $entity->author_id = 1;
            $entity->save();

    		// Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach($actions as $action){
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::with('action', 'entity')->where('entity_id', $entity->id)->get();
            $mass = [];

            foreach($actionentities as $actionentity){

               $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

               $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
           };

           DB::table('rights')->insert($mass);

           $actionentities = $actionentities->pluck('id')->toArray();

        	// Получаем все существующие разрешения (allow)
           $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

           $mass = [];
        	// Генерируем права на полный доступ
           foreach($rights as $right){
               $mass[] = ['right_id' => $right->id, 'role_id' => 1, 'system_item' => 1];
           };

           DB::table('right_role')->insert($mass);

           $entity_id = $entity->id;
       }

       $entity_page = EntityPage::firstOrCreate(['entity_id' => $entity_id, 'page_id' => $page_id]);

       $navigation = Navigation::where(['alias' => 'left-sidebar', 'site_id' => 1])->first();

       $navigation_id = $navigation->id;

       $menu = Menu::where('tag', 'sale')->first();

       $parent_menu_id = $menu->id;

       $menu = new Menu;
       $menu->name = 'Показатели';
       $menu->alias = 'admin/indicators';
       $menu->tag = 'indicators';
       $menu->parent_id = $parent_menu_id;
       $menu->page_id = $page_id;
       $menu->navigation_id = $navigation_id;
       $menu->system_item = 1;
       $menu->author_id = 1;
       $menu->display = 1;
       $menu->save();

   }
}
