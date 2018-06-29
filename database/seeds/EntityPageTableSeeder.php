<?php

use Illuminate\Database\Seeder;

class EntityPageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('entity_page')->insert([
        	[
	        	'entity_id' => 1, 
                'page_id' => 2, 
        	],
        	[
	        	'entity_id' => 2, 
                'page_id' => 1, 
        	],
        	[
	        	'entity_id' => 3, 
                'page_id' => 4, 
        	],
        	[
	        	'entity_id' => 4, 
                'page_id' => 3, 
        	],
        	[
	        	'entity_id' => 5, 
                'page_id' => 9, 
        	],
        	[
	        	'entity_id' => 6, 
                'page_id' => 5, 
        	],
        	[
	        	'entity_id' => 7, 
                'page_id' => 8, 
        	],
        	[
	        	'entity_id' => 8, 
                'page_id' => 8, 
        	],
        	[
	        	'entity_id' => 9, 
                'page_id' => 8, 
        	],
        	[
	        	'entity_id' => 11, 
                'page_id' => 10, 
        	],
        	[
	        	'entity_id' => 12, 
                'page_id' => 7, 
        	],
        	[
	        	'entity_id' => 13, 
                'page_id' => 13, 
        	],
        	[
	        	'entity_id' => 14, 
                'page_id' => 14, 
        	],
        	[
	        	'entity_id' => 16, 
                'page_id' => 14, 
        	],
        	[
	        	'entity_id' => 17, 
                'page_id' => 12, 
        	],
            [
                'entity_id' => 18, 
                'page_id' => 11, 
            ],
            [
                'entity_id' => 19, 
                'page_id' => 16, 
            ],
            [
                'entity_id' => 20, 
                'page_id' => 17, 
            ],
            [
                'entity_id' => 21, 
                'page_id' => 18, 
            ],
            [
                'entity_id' => 22, 
                'page_id' => 36, 
            ],
            [
                'entity_id' => 23, 
                'page_id' => 37, 
            ],
            [
                'entity_id' => 24, 
                'page_id' => 38, 
            ],
            [
                'entity_id' => 26, 
                'page_id' => 39, 
            ],
            [
                'entity_id' => 27, 
                'page_id' => 40, 
            ],
            [
                'entity_id' => 28, 
                'page_id' => 40, 
            ],
            [
                'entity_id' => 29, 
                'page_id' => 41, 
            ],
            [
                'entity_id' => 30, 
                'page_id' => 42, 
            ],

        ]);
    }
}
