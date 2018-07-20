<?php

use Illuminate\Database\Seeder;

class GoodsModesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('goods_modes')->insert([
            [
                'name' => 'Товар',
                'description' => '',
                'alias' => 'goods',
            ],

    	]);
    }
}
