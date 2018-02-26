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
                'page_id' => 12, 
        	],
        	[
	        	'entity_id' => 17, 
                'page_id' => 11, 
        	],




        ]);
    }
}
