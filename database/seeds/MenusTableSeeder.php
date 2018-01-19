<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->insert([
            [
                'menu_name' => 'ЦУП',
                'menu_icon' => 'icon-mcc',
                'menu_parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
            ],
            [
                'menu_name' => 'Тест для сотрудников',
                'menu_icon' => 'icon-sale',
                'menu_parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
            ],
            [
                'menu_name' => 'Маркетинг',
                'menu_icon' => 'icon-marketing',
                'menu_parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
            ],
            [
                'menu_name' => 'Справочники',
                'menu_icon' => 'icon-guide',
                'menu_parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
            ],
            [
                'menu_name' => 'Настройки',
                'menu_icon' => 'icon-settings',
                'menu_parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
            ],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 1,
		        'page_id' => 1,
                'navigation_id' => 1,
        	],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 1,
		        'page_id' => 2,
                'navigation_id' => 1,
        	],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 1,
		        'page_id' => 3,
                'navigation_id' => 1,
        	],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 1,
		        'page_id' => 4,
                'navigation_id' => 1,
        	],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 1,
		        'page_id' => 5,
                'navigation_id' => 1,
        	],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 2,
		        'page_id' => 6,
                'navigation_id' => 1,
        	],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 3,
		        'page_id' => 7,
                'navigation_id' => 1,
        	],
        	// [
		       //  'menu_name' => null,
         //        'menu_icon' => null,
         //        'menu_parent_id' => 3,
		       //  'page_id' => 8,
         //        'navigation_id' => 1,
        	// ],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 4,
		        'page_id' => 8,
                'navigation_id' => 1,
        	],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 4,
		        'page_id' => 9,
                'navigation_id' => 1,
        	],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 5,
		        'page_id' => 10,
                'navigation_id' => 1,
        	],
        	[
		        'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 5,
		        'page_id' => 11,
                'navigation_id' => 1,
        	],
            [
                'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 5,
                'page_id' => 12,
                'navigation_id' => 1,
            ],
            [
                'menu_name' => null,
                'menu_icon' => null,
                'menu_parent_id' => 3,
                'page_id' => 13,
                'navigation_id' => 1,
            ],

        ]);
    }
}
