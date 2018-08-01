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

        // $mass = [];
        for ($i = 1; $i < 28; $i++) { 
            $mass[] = [
                'entity_id' => $i,
                'page_id' => $i,
            ];
        }
        $mass[] = [
            'entity_id' => 27,
            'page_id' => 27,
        ];
        $mass[] = [
            'entity_id' => 28,
            'page_id' => 27,
        ];

        $mass[] = [
            'entity_id' => 29,
            'page_id' => 28,
        ];
        $mass[] = [
            'entity_id' => 30,
            'page_id' => 28,
        ];
        $mass[] = [
            'entity_id' => 31,
            'page_id' => 28,
        ];
        // $mass[] = [
        //     'entity_id' => 54,
        //     'page_id' => 31,
        // ];

        DB::table('entity_page')->insert($mass);
    }
}
