<?php

use Illuminate\Database\Seeder;

use App\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
        	[
                'name' => 'Продажа со склада',
                'alias' => 'sale-from-stock'
        	],
            [
                'name' => 'Продажа под заказ',
                'alias' => 'sale-for-order'
            ],
            [
                'name' => 'Продажа под производство',
                'alias' => 'sale-for-production'
            ],
        ]);
    }
}
