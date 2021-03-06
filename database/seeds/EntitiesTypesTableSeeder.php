<?php

use Illuminate\Database\Seeder;
use App\EntitiesType;

class EntitiesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EntitiesType::insert([
        	[
		        'name' => 'Сайт',
		        'alias' => 'site',
        	],
            [
                'name' => 'ТМЦ',
                'alias' => 'cmv',
            ],
            [
                'name' => 'Процесс',
                'alias' => 'process',
            ],
        ]);
    }
}
