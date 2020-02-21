<?php

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->insert([
        	[
                'name' => 'Иркутск',
                'prepositional_case' => 'Иркутске',
                'alias' => 'irkutsk',
		        'region_id' => 1,
		        'code' => 83952,
		        'vk_external_id' => 57,
                'system' => true,
                'country_id' => 1
        	],
            [
                'name' => 'Ангарск',
                'prepositional_case' => 'Ангарске',
                'alias' => 'angarsk',
                'region_id' => 1,
                'code' => 83955,
                'vk_external_id' => 19,
                'system' => true,
                'country_id' => 1
            ],
            [
                'name' => 'Братск',
                'prepositional_case' => 'Братске',
                'alias' => 'bratsk',
                    'region_id' => 1,
                'code' => 83953,
                'vk_external_id' => 32,
                'system' => true,
                'country_id' => 1
            ],
            [
                'name' => 'Усолье-Сибирское',
                'prepositional_case' => 'Усолье-Сибирском',   
                'alias' => 'usole-sibirskoe',
                'region_id' => 1,
                'code' => 839543,
                'vk_external_id' => 728,
                'system' => true,
                'country_id' => 1
            ],  
        ]);
    }
}
