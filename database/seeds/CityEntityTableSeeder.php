<?php

use Illuminate\Database\Seeder;

class CityEntityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('city_entity')->insert([
    		[
    			'city_id' => 1,
    			'entity_id' => 1,
    			'entity' => 'news',
    		],
            [
                'city_id' => 2,
    			'entity_id' => 1,
    			'entity' => 'news',
            ],
            [
                'city_id' => 4,
    			'entity_id' => 1,
    			'entity' => 'news',
            ],
            [
    			'city_id' => 1,
    			'entity_id' => 2,
    			'entity' => 'news',
    		],
            [
                'city_id' => 2,
    			'entity_id' => 2,
    			'entity' => 'news',
            ],
            [
                'city_id' => 4,
    			'entity_id' => 2,
    			'entity' => 'news',
            ],
            [
    			'city_id' => 1,
    			'entity_id' => 3,
    			'entity' => 'news',
    		],
            [
                'city_id' => 2,
    			'entity_id' => 3,
    			'entity' => 'news',
            ],
            [
                'city_id' => 4,
    			'entity_id' => 3,
    			'entity' => 'news',
            ],
            [
    			'city_id' => 1,
    			'entity_id' => 4,
    			'entity' => 'news',
    		],
            [
                'city_id' => 2,
    			'entity_id' => 4,
    			'entity' => 'news',
            ],
       //      [
       //          'city_id' => 4,
    			// 'entity_id' => 4,
    			// 'entity' => 'news',
       //      ],
            [
    			'city_id' => 1,
    			'entity_id' => 5,
    			'entity' => 'news',
    		],
            [
                'city_id' => 2,
    			'entity_id' => 5,
    			'entity' => 'news',
            ],
       //      [
       //          'city_id' => 4,
    			// 'entity_id' => 5,
    			// 'entity' => 'news',
       //      ],
            [
    			'city_id' => 1,
    			'entity_id' => 6,
    			'entity' => 'news',
    		],
            [
                'city_id' => 2,
    			'entity_id' => 6,
    			'entity' => 'news',
            ],
       //      [
       //          'city_id' => 4,
    			// 'entity_id' => 6,
    			// 'entity' => 'news',
       //      ],

    	]);
    }
}
