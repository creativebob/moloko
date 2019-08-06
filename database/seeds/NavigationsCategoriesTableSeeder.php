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
            'system' => true,
        ],
        [
            'name' => 'Главное меню',
            'tag' => 'general',
            'system' => true,
        ],
        [
            'name' => 'Соц. сети',
            'tag' => 'social',
            'system' => true,
        ],
        [
            'name' => 'Футер',
            'tag' => 'footer',
            'system' => true,
        ],

    ]);
  }
}
