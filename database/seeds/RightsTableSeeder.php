<?php

use Illuminate\Database\Seeder;

class RightsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rights')->insert([

        	['right_name' => 'Добавление пользователя', 'right_action' => 'create-user', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
        	['right_name' => 'Редактирование пользователя', 'right_action' => 'update-user', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
        	['right_name' => 'Просмотр пользователя', 'right_action' => 'view-user', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
        	['right_name' => 'Удаление пользователя', 'right_action' => 'delete-user', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 	
            ['right_name' => 'Просмотр списка пользователей', 'right_action' => 'index-user', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр системных пользователей', 'right_action' => 'system-user', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр всех пользователей', 'right_action' => 'get-users', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 

            ['right_name' => 'Добавление компании', 'right_action' => 'create-company', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Редактирование компании', 'right_action' => 'update-company', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр компании', 'right_action' => 'view-company', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Удаление компании', 'right_action' => 'delete-company', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1],   
            ['right_name' => 'Просмотр списка компаний', 'right_action' => 'index-company', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр системных компаний', 'right_action' => 'system-company', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр всех компаний', 'right_action' => 'get-companies', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 

            ['right_name' => 'Добавление правила', 'right_action' => 'create-right', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Редактирование правила', 'right_action' => 'update-right', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр правила', 'right_action' => 'view-right', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Удаление правила', 'right_action' => 'delete-right', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1],   
            ['right_name' => 'Просмотр списка правил', 'right_action' => 'index-right', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр системных правил', 'right_action' => 'system-right', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр всех прав', 'right_action' => 'get-rights', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 

            ['right_name' => 'Добавление сущности', 'right_action' => 'create-entity', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Редактирование сущности', 'right_action' => 'update-entity', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр сущности', 'right_action' => 'view-entity', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Удаление сущности', 'right_action' => 'delete-entity', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1],   
            ['right_name' => 'Просмотр списка сущностей', 'right_action' => 'index-entity', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр системных сущностей', 'right_action' => 'system-entity', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр всех сущностей', 'right_action' => 'get-entities', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 

            ['right_name' => 'Добавление отдела', 'right_action' => 'create-department', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Редактирование отдела', 'right_action' => 'update-department', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр отдела', 'right_action' => 'view-department', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Удаление отдела', 'right_action' => 'delete-department', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1],   
            ['right_name' => 'Просмотр списка отделов', 'right_action' => 'index-department', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр системных отделов', 'right_action' => 'system-department', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр всех отделов', 'right_action' => 'get-departments', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 

            ['right_name' => 'Добавление группы', 'right_action' => 'create-role', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Редактирование группы', 'right_action' => 'update-role', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр группы', 'right_action' => 'view-role', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Удаление группы', 'right_action' => 'delete-role', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1],   
            ['right_name' => 'Просмотр списка групп', 'right_action' => 'index-role', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр системных групп', 'right_action' => 'system-role', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 
            ['right_name' => 'Просмотр всех групп', 'right_action' => 'get-roles', 'category_right_id' => 1, 'company_id' = > null, 'system_item' => 1], 

            ['right_name' => 'Просмотр всех филиалов', 'right_action' => 'getall-department', 'category_right_id' => 2, 'company_id' = > 1, 'system_item' => null], 
            ['right_name' => 'Иркутский филиал', 'right_action' => 1, 'category_right_id' => 2, 'company_id' = > 1, 'system_item' => null], 
            ['right_name' => 'Улан-Удэнский филиал', 'right_action' => 2, 'category_right_id' => 2, 'company_id' = > 1, 'system_item' => null], 
        ]);
    }
}
