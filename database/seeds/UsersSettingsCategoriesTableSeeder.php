<?php

use Illuminate\Database\Seeder;

use App\UsersSettingsCategory;

class UsersSettingsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UsersSettingsCategory::insert([
        	[
                'name' => 'Настройки отображения каталога',
                'slug' => \Str::slug('Настройки отображения каталога'),
                'level' => 1,
                'alias' => 'lead-show-catalog',
        	],
        ]);
    }
}
