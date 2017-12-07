<?php

use Illuminate\Database\Seeder;

class CategotyRightTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category_rights')->insert([
        	['category_right_name' => 'Функциональные права'], 
        	['category_right_name' => 'Филиальные права']
        ]);
    }
}