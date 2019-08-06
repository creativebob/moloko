<?php

use Illuminate\Database\Seeder;

// Модели
use App\Claim;
use App\Navigation;
use App\Menu;
use App\Page;
use App\Entity;
use App\EntityPage;
use App\Action;
use App\ActionEntity;
use App\Right;

class FeedbackTableSeeder extends Seeder
{

    public function run()
    {

    	$page = Page::where('alias', 'feedback')->first();

    	if ($page) {
    		$page_id = $page->id;
    	} else {
    		$page = new Page;
    		$page->name = 'Отзывы';
    		$page->title = 'Отзывы';
    		$page->description = 'Отзывы';
    		$page->alias = 'feedback';
    		$page->site_id = 1;
    		$page->company_id = null;
    		$page->display = 1;
    		$page->system = 1;
    		$page->author_id = 1;
    		$page->save();

    		$page_id = $page->id;
    	}

    	$entity = Entity::where('alias', 'feedback')->first();
    	if ($entity) {
    		$entity_id = $entity->id;
    	} else {
    		$entity = new Entity;
    		$entity->name = 'Отзывы';
    		$entity->alias = 'feedback';
    		$entity->model = 'Feedback';
    		$entity->rights_minus = null;
    		$entity->system = 1;
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

    		$entity_id = $entity->id;
    	}

    	$entity_page = EntityPage::firstOrCreate(['entity_id' => $entity_id, 'page_id' => $page_id]);

    	$navigation = Navigation::where(['alias' => 'left-sidebar', 'site_id' => 1])->first();

    	$navigation_id = $navigation->id;

    	$menu = Menu::where('tag', 'marketing')->first();

    	$parent_menu_id = $menu->id;

    	$menu = new Menu;
    	$menu->name = 'Отзывы';
    	$menu->alias = 'admin/feedback';
    	$menu->tag = 'feedback';
    	$menu->parent_id = $parent_menu_id;
    	$menu->page_id = $page_id;
    	$menu->navigation_id = $navigation_id;
    	$menu->system = 1;
    	$menu->author_id = 1;
    	$menu->display = 1;
    	$menu->save();

    }
}
