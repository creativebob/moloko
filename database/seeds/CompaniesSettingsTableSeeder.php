<?php

use Illuminate\Database\Seeder;

use App\CompaniesSetting;
use App\CompaniesSettingsCategory;

class CompaniesSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settingsCategories = CompaniesSettingsCategory::get();

        CompaniesSetting::insert([
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
            [
                'name' => 'Показатели клиентской базы',
                'alias' => 'clients_indicators',
                'category_id' => $settingsCategories->firstWhere('alias', 'cron')->id
            ],
            [
                'name' => 'Перерасчет скидок',
                'alias' => 'discounts',
                'category_id' => $settingsCategories->firstWhere('alias', 'cron')->id
            ],
        ]);
    }
}
