<?php

use Illuminate\Database\Seeder;

use App\CatalogsType;

class CatalogsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CatalogsType::insert([
        	[
		        'name' => 'Товары',
                'alias' => 'goods',
        	],
            [
                'name' => 'Услуги',
                'alias' => 'services',
            ],

        ]);
    }
}
