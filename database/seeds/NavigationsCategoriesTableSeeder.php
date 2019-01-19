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
            'system_item' => 1,
        ],
        [
            'name' => 'Главное меню',
            'tag' => 'general',
            'system_item' => 1,
        ],
        [
            'name' => 'Соц. сети',
            'tag' => 'social',
            'system_item' => 1,
        ],
        [
            'name' => 'Футер',
            'tag' => 'footer',
            'system_item' => 1,
        ],

    ]);
  }
}
