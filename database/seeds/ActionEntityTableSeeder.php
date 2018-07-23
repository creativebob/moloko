<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Entity;
use App\Action;


class ActionEntityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $actions = Action::get();
        $entities = Entity::whereNull('rights_minus')->get();
       	$mass = [];

        foreach($entities as $entity){
        	foreach($actions as $action){

         		$mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];

        	};
        }

 		DB::table('action_entity')->insert($mass);

        // DB::table('action_entity')->insert([
        // 	['action_id' => '1', 'entity_id' => '1', 'alias_action_entity' => 'view-users'], 
        // 	['action_id' => '2', 'entity_id' => '1', 'alias_action_entity' => 'index-users'], 
        // 	['action_id' => '3', 'entity_id' => '1', 'alias_action_entity' => 'create-users'], 
        // 	['action_id' => '4', 'entity_id' => '1', 'alias_action_entity' => 'edit-users'], 
        // 	['action_id' => '5', 'entity_id' => '1', 'alias_action_entity' => 'delete-users'], 
        // 	['action_id' => '6', 'entity_id' => '1', 'alias_action_entity' => 'system-users'], 
        // 	['action_id' => '7', 'entity_id' => '1', 'alias_action_entity' => 'getall-users'], 

        // 	['action_id' => '1', 'entity_id' => '2', 'alias_action_entity' => 'view-companies'], 
        // 	['action_id' => '2', 'entity_id' => '2', 'alias_action_entity' => 'index-companies'], 
        // 	['action_id' => '3', 'entity_id' => '2', 'alias_action_entity' => 'create-companies'], 
        // 	['action_id' => '4', 'entity_id' => '2', 'alias_action_entity' => 'edit-companies'], 
        // 	['action_id' => '5', 'entity_id' => '2', 'alias_action_entity' => 'delete-companies'], 
        // 	['action_id' => '6', 'entity_id' => '2', 'alias_action_entity' => 'system-companies'], 
        // 	['action_id' => '7', 'entity_id' => '2', 'alias_action_entity' => 'getall-companies'], 

        // 	['action_id' => '1', 'entity_id' => '3', 'alias_action_entity' => 'view-departments'], 
        // 	['action_id' => '2', 'entity_id' => '3', 'alias_action_entity' => 'index-departments'], 
        // 	['action_id' => '3', 'entity_id' => '3', 'alias_action_entity' => 'create-departments'], 
        // 	['action_id' => '4', 'entity_id' => '3', 'alias_action_entity' => 'edit-departments'], 
        // 	['action_id' => '5', 'entity_id' => '3', 'alias_action_entity' => 'delete-departments'], 
        // 	['action_id' => '6', 'entity_id' => '3', 'alias_action_entity' => 'system-departments'], 
        // 	['action_id' => '7', 'entity_id' => '3', 'alias_action_entity' => 'getall-departments'], 

        // 	['action_id' => '1', 'entity_id' => '4', 'alias_action_entity' => 'view-areas'], 
        // 	['action_id' => '2', 'entity_id' => '4', 'alias_action_entity' => 'index-areas'], 
        // 	['action_id' => '3', 'entity_id' => '4', 'alias_action_entity' => 'create-areas'], 
        // 	['action_id' => '4', 'entity_id' => '4', 'alias_action_entity' => 'edit-areas'], 
        // 	['action_id' => '5', 'entity_id' => '4', 'alias_action_entity' => 'delete-areas'], 
        // 	['action_id' => '6', 'entity_id' => '4', 'alias_action_entity' => 'system-areas'], 
        // 	['action_id' => '7', 'entity_id' => '4', 'alias_action_entity' => 'getall-areas'],  
        // ]);
    }
}
