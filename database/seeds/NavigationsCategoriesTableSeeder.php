<?php

use Illuminate\Database\Seeder;

class NavigationsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('navigations_categories')->insert([
      [
        'name' => 'Навигация по сайту',
        'tag' => 'main',
        'category_status' => 1,
        'system_item' => 1,
      ], 
      [
        'name' => 'Главное меню',
        'tag' => 'general',
        'category_status' => 1,
        'system_item' => 1,
      ], 
      [
        'name' => 'Соц. сети',
        'tag' => 'social',
        'category_status' => 1,
        'system_item' => 1,
      ], 
      [
        'name' => 'Футер',
        'tag' => 'footer',
        'category_status' => 1,
        'system_item' => 1,
      ], 

    ]);
    }
}
