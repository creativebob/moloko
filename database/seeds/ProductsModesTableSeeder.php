<?php

use Illuminate\Database\Seeder;

class ProductsModesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products_modes')->insert([
    		[
    			'name' => 'Товар',
                'description' => '',
                'alias' => 'goods',
                'type' => 'goods',
    		],
            [
                'name' => 'Монтаж',
                'description' => '',
                'alias' => 'installs',
                'type' => 'services',
            ],
            [
                'name' => 'Доставка',
                'description' => '',
                'alias' => 'deliveries',
                'type' => 'services',
            ],
            [
                'name' => 'Замер',
                'description' => '',
                'alias' => 'measurements',
                'type' => 'services',
            ],
            [
                'name' => 'Материал',
                'description' => '',
                'alias' => 'materials',
                'type' => 'raws',
            ],
            [
                'name' => 'Полуфабрикат',
                'description' => '',
                'alias' => 'semis',
                'type' => 'raws',
            ],
    	]);
    }
}
