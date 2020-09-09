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

        ]);
    }
}
