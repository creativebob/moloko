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
    		],
            [
                'name' => 'Монтаж',
            ],
            [
                'name' => 'Доставка',
            ],
            [
                'name' => 'Замер',
            ],
    	]);
    }
}
