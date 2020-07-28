<?php

use Illuminate\Database\Seeder;

use App\SettingsCategory;

class SettingsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SettingsCategory::insert([
        	[
                'name' => 'Настройки для продажи',
                'slug' => \Str::slug('Настройки для продажи'),
                'level' => 1,
                'alias' => 'sales',
        	],

        ]);
    }
}
