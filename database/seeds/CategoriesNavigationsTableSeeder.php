<?php

use Illuminate\Database\Seeder;

class CategoriesNavigationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories_navigations')->insert([
        	[
        		'category_navigation_name' => 'Управление сайтом',
        		'system_item' => 1,
        	], 
        	[
        		'category_navigation_name' => 'Навигации сайта',
        		'system_item' => 1,
        	], 
        	[
        		'category_navigation_name' => 'Фотогалерея',
        		'system_item' => 1,
        	], 
        	
        ]);
    }
}
