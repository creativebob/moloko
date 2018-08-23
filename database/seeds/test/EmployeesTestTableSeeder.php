<?php

use Illuminate\Database\Seeder;

class EmployeesTestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->insert([
        	[	
        		'staffer_id' => 1,
                'company_id' => 1,
		        'user_id' => 4,
                'employment_date' => '2017-12-01',
                'dismissal_date' => null,
                'dismissal_description' => null,
        	],
            [   
                'staffer_id' => 2,
                'company_id' => 1,
                'user_id' => 5,
                'employment_date' => '2017-12-01',
                'dismissal_date' => null,
                'dismissal_description' => null,
            ],
            [   
                'staffer_id' => 3,
                'company_id' => 1,
                'user_id' => 6,
                'employment_date' => '2017-12-01',
                'dismissal_date' => null,
                'dismissal_description' => null,
            ],
            [   
                'staffer_id' => 4,
                'company_id' => 1,
                'user_id' => 7,
                'employment_date' => '2017-12-01',
                'dismissal_date' => null,
                'dismissal_description' => null,
            ],
            [   
                'staffer_id' => 5,
                'company_id' => 1,
                'user_id' => 8,
                'employment_date' => '2017-12-01',
                'dismissal_date' => null,
                'dismissal_description' => null,
            ],
             [   
                'staffer_id' => 6,
                'company_id' => 1,
                'user_id' => 9,
                'employment_date' => '2017-12-01',
                'dismissal_date' => null,
                'dismissal_description' => null,
            ],
            [   
                'staffer_id' => 7,
                'company_id' => 1,
                'user_id' => 10,
                'employment_date' => '2017-12-01',
                'dismissal_date' => null,
                'dismissal_description' => null,
            ],
            [   
                'staffer_id' => 8,
                'company_id' => 1,
                'user_id' => 11,
                'employment_date' => '2017-12-01',
                'dismissal_date' => null,
                'dismissal_description' => null,
            ],

        ]);
    }
}
