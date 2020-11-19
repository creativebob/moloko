<?php

use Illuminate\Database\Seeder;

use App\OutletsSettingsCategory;

class OutletsSettingsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OutletsSettingsCategory::insert([
        	[
                'name' => 'Способы расчета',
                'slug' => \Str::slug('Способы расчета'),
                'level' => 1,
                'alias' => 'payments-types',
        	],
            [
                'name' => 'Касса',
                'slug' => \Str::slug('Касса'),
                'level' => 1,
                'alias' => 'cash-register',
            ],
            [
                'name' => 'Списания',
                'slug' => \Str::slug('Списания'),
                'level' => 1,
                'alias' => 'dismiss',
            ],
            [
                'name' => 'Прочие',
                'slug' => \Str::slug('Прочие'),
                'level' => 1,
                'alias' => 'others',
            ],
        ]);
    }
}
