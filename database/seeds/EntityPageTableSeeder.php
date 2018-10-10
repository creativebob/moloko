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
            'entity_id' => 28,
            'page_id' => 28,
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
            'entity_id' => 52,
            'page_id' => 29,
        ];
        $mass[] = [
            'entity_id' => 53,
            'page_id' => 30,
        ];
        $mass[] = [
            'entity_id' => 54,
            'page_id' => 31,
        ];
        $mass[] = [
            'entity_id' => 55,
            'page_id' => 36,
        ];
        $mass[] = [
            'entity_id' => 56,
            'page_id' => 37,
        ];
        $mass[] = [
            'entity_id' => 57,
            'page_id' => 39,
        ];
        $mass[] = [
            'entity_id' => 58,
            'page_id' => 40,
        ];
        $mass[] = [
            'entity_id' => 61,
            'page_id' => 41,
        ];
        $mass[] = [
            'entity_id' => 62,
            'page_id' => 42,
        ];
        // $mass[] = [
        //     'entity_id' => 65,
        //     'page_id' => 43,
        // ];

        $mass[] = [
            'entity_id' => 49,
            'page_id' => 33,
        ];
        $mass[] = [
            'entity_id' => 50,
            'page_id' => 34,
        ];
        $mass[] = [
            'entity_id' => 51,
            'page_id' => 35,
        ];

        $mass[] = [
            'entity_id' =>66,
            'page_id' => 44,
        ];
        $mass[] = [
            'entity_id' => 67,
            'page_id' => 45,
        ];

        $mass[] = [
            'entity_id' => 70,
            'page_id' => 46,
        ];

        // $mass[] = [
        //     'entity_id' => 31,
        //     'page_id' => 28,
        // ];
        // $mass[] = [
        //     'entity_id' => 54,
        //     'page_id' => 31,
        // ];

        DB::table('entity_page')->insert($mass);
    }
}
