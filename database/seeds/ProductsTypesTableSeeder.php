<?php

use Illuminate\Database\Seeder;

class ProductsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products_types')->insert([
    		[
    			'name' => 'Товар',
                'description' => '',
                'alias' => 'goods',
                'type' => 'goods',
    		],
            [
                'name' => 'Монтаж',
                'description' => '',
                'alias' => 'install',
                'type' => 'service',
            ],
            [
                'name' => 'Доставка',
                'description' => '',
                'alias' => 'delivery',
                'type' => 'service',
            ],
            [
                'name' => 'Замер',
                'description' => '',
                'alias' => 'measurement',
                'type' => 'service',
            ],
    	]);
    }
}
