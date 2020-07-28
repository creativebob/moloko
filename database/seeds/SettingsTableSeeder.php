<?php

use Illuminate\Database\Seeder;

use App\Setting;
use App\SettingsCategory;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settingsCategories = SettingsCategory::get();

        Setting::insert([
        	[
                'name' => 'Продажа со склада',
                'alias' => 'sale-from-stock',
                'category_id' => $settingsCategories->firstWhere('alias', 'sales')->id
        	],
            [
                'name' => 'Продажа под заказ',
                'alias' => 'sale-for-order',
                'category_id' => $settingsCategories->firstWhere('alias', 'sales')->id
            ],
            [
                'name' => 'Продажа под производство',
                'alias' => 'sale-for-production',
                'category_id' => $settingsCategories->firstWhere('alias', 'sales')->id
            ],
        ]);
    }
}
