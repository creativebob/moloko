<?php

use Illuminate\Database\Seeder;

class MetricEntityTableSeeder extends Seeder
{

    public function run()
    {
        $mass[] = [
            'metric_id' => 1,
            'entity_id' => 2,
            'entity' => 'products_categories',
        ];
        $mass[] = [
            'metric_id' => 2,
            'entity_id' => 2,
            'entity' => 'products_categories',
        ];

        for ($i=7; $i <= 41; $i++) { 
            $mass[] = [
                'metric_id' => 3,
                'entity_id' => $i,
                'entity' => 'products_categories',
            ];
        }

        for ($i=7; $i <= 41; $i++) { 
            $mass[] = [
                'metric_id' => 6,
                'entity_id' => $i,
                'entity' => 'products_categories',
            ];
        }


        DB::table('metric_entity')->insert($mass);
    }
}
