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
        'name' => 'Управление сайтом',
        'system_item' => 1,
      ], 
      [
        'name' => 'Навигации сайта',
        'system_item' => 1,
      ], 
      [
        'name' => 'Фотогалерея',
        'system_item' => 1,
      ], 

    ]);
    }
}
