<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\ActionEntity;
use App\Action;
use App\Entity;

class RightsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $actions = Action::get();
        $actionentities = Actionentity::get();
        $mass = [];

        foreach($actionentities as $actionentity){

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
        };

        DB::table('rights')->insert($mass);

        // DB::table('rights')->insert([

        // 	['right_name' => 'Просмотр пользователя', 'object_entity' => 1, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1], 
        //  ['right_name' => 'Просмотр списка пользователей', 'object_entity' => 2, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1], 
        //  ['right_name' => 'Добавление пользователя', 'object_entity' => 3, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1], 
        // 	['right_name' => 'Редактирование пользователя', 'object_entity' => 4, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1], 
        // 	['right_name' => 'Удаление пользователя', 'object_entity' => 5, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1], 	
        //  ['right_name' => 'Просмотр системных пользователей', 'object_entity' => 6, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1], 
        //  ['right_name' => 'Просмотр всех пользователей', 'object_entity' => 7, 'category_right_id' => 1, 'company_id' => null, 'system_item' => 1], 


          // ['right_name' => 'Просмотр всех филиалов', 'object_entity' => 'getall-department', 'category_right_id' => 2, 'company_id' => 1, 'system_item' => null], 
          // ['right_name' => 'Иркутский филиал', 'object_entity' => 1, 'category_right_id' => 2, 'company_id' => 1, 'system_item' => null], 
          // ['right_name' => 'Улан-Удэнский филиал', 'object_entity' => 2, 'category_right_id' => 2, 'company_id' => 1, 'system_item' => null], 

        // ]);
    }
}
