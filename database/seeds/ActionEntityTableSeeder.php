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
        $entities = Entity::where('rights', true)->get();
       	$mass = [];

        foreach($entities as $entity){
        	foreach($actions as $action){

         		$mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];

        	};
        }

 		DB::table('action_entity')->insert($mass);

    }
}
