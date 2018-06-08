<?php

use Illuminate\Database\Seeder;

class StaffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('staff')->insert([
        	[	
        		'company_id' => 1,
		        'user_id' => 4,
                'position_id' => 1,
		        'department_id' => 1,
                'filial_id' => 1,
                'author_id' => 1,
                'display' => 1,
        	],
            [   
                'company_id' => 1,
                'user_id' => 5,
                'position_id' => 2,
                'department_id' => 2,
                'filial_id' => 1,
                'author_id' => 1,
                'display' => null,
            ],
            [   
                'company_id' => 1,
                'user_id' => 6,
                'position_id' => 2,
                'department_id' => 2,
                'filial_id' => 1,
                'author_id' => 1,
                'display' => null,
            ],
            [   
                'company_id' => 1,
                'user_id' => 7,
                'position_id' => 4,
                'department_id' => 3,
                'filial_id' => 1,
                'author_id' => 1,
                'display' => null,
            ],
            [   
                'company_id' => 1,
                'user_id' => 8,
                'position_id' => 1,
                'department_id' => 4,
                'filial_id' => 4,
                'author_id' => 1,
                'display' => null,
            ],
            [   
                'company_id' => 1,
                'user_id' => 9,
                'position_id' => 2,
                'department_id' => 5,
                'filial_id' => 4,
                'author_id' => 1,
                'display' => null,
            ],
            [   
                'company_id' => 1,
                'user_id' => 10,
                'position_id' => 2,
                'department_id' => 5,
                'filial_id' => 5,
                'author_id' => 1,
                'display' => null,
            ],
            [   
                'company_id' => 2,
                'user_id' => 11,
                'position_id' => 1,
                'department_id' => 6,
                'filial_id' => 6,
                'author_id' => 1,
                'display' => null,
            ],
            [   
                'company_id' => 1,
                'user_id' => 14,
                'position_id' => 6,
                'department_id' => 2,
                'filial_id' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // Шторка
            [   
                'company_id' => 4,
                'user_id' => 15,
                'position_id' => 1,
                'department_id' => 8,
                'filial_id' => 8,
                'author_id' => 1,
            ],
        ]);
    }
}
