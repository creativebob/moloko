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
                'alias' => 'clients-indicators',
                'category_id' => $settingsCategories->firstWhere('alias', 'cron')->id
            ],
            [
                'name' => 'Перерасчет скидок',
                'alias' => 'discounts-recalculate',
                'category_id' => $settingsCategories->firstWhere('alias', 'cron')->id
            ],
            [
                'name' => 'Приоритет компании',
                'alias' => 'search-company-priority',
                'category_id' => $settingsCategories->firstWhere('alias', 'leads')->id
            ],
        ]);
    }
}
