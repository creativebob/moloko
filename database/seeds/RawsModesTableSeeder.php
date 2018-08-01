<?php

use Illuminate\Database\Seeder;

class RawsModesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('raws_modes')->insert([
            [
                'name' => 'Сырье',
                'description' => '',
                'alias' => 'raws',
            ],

    	]);
    }
}
