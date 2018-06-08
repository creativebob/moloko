<?php

use Illuminate\Database\Seeder;

class MetricEntityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('metric_entity')->insert([
    		[
    			'metric_id' => 1,
    			'entity_id' => 1,
    			'entity' => 'products',
    		],
    		[
    			'metric_id' => 2,
    			'entity_id' => 1,
    			'entity' => 'products',
    		],
    	]);
    }
}
