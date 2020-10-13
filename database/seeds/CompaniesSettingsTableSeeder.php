<?php

use Illuminate\Database\Seeder;

use App\UsersSetting;
use App\UsersSettingsCategory;

class UsersSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settingsCategories = UsersSettingsCategory::get();

        UsersSetting::insert([
        	[
                'name' => 'Хиты',
                'alias' => 'lead-catalog-show-hit',
                'category_id' => $settingsCategories->firstWhere('alias', 'lead-show-catalog')->id
        	],
            [
                'name' => 'Новинки',
                'alias' => 'lead-catalog-show-new',
                'category_id' => $settingsCategories->firstWhere('alias', 'lead-show-catalog')->id
            ],
            [
                'name' => 'Нет на складе',
                'alias' => 'lead-catalog-show-out-of-stock',
                'category_id' => $settingsCategories->firstWhere('alias', 'lead-show-catalog')->id
            ],
            [
                'name' => 'Приоритет',
                'alias' => 'lead-catalog-show-priority',
                'category_id' => $settingsCategories->firstWhere('alias', 'lead-show-catalog')->id
            ],
            [
                'name' => 'Б/у',
                'alias' => 'lead-catalog-show-used',
                'category_id' => $settingsCategories->firstWhere('alias', 'lead-show-catalog')->id
            ],
            [
                'name' => 'Под заказ',
                'alias' => 'lead-catalog-show-preorder',
                'category_id' => $settingsCategories->firstWhere('alias', 'lead-show-catalog')->id
            ],
        ]);
    }
}
