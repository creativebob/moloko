<?php

use Illuminate\Database\Seeder;

use App\CompaniesSettingsCategory;

class CompaniesSettingsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompaniesSettingsCategory::insert([
        	[
                'name' => 'Настройки для продажи',
                'slug' => \Str::slug('Настройки для продажи'),
                'level' => 1,
                'alias' => 'sales',
        	],
            [
                'name' => 'Настройки для автоматического расчета',
                'slug' => \Str::slug('Настройки для автоматического расчета'),
                'level' => 1,
                'alias' => 'cron',
            ],
            [
                'name' => 'Настройки для лида',
                'slug' => \Str::slug('Настройки для лида'),
                'level' => 1,
                'alias' => 'leads',
            ],
            [
                'name' => 'Система лояльности',
                'slug' => \Str::slug('Система лояльности'),
                'level' => 1,
                'alias' => 'loyalty-system',
            ],
        ]);
    }
}
