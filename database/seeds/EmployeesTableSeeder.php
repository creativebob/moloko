<?php

use Illuminate\Database\Seeder;

class EmployeesTableSeeder extends Seeder
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
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
        	],
            [   
                'staffer_id' => 2,
                'company_id' => 1,
                'user_id' => 8,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],
            [   
                'staffer_id' => 3,
                'company_id' => 1,
                'user_id' => 11,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],
            [   
                'staffer_id' => 4,
                'company_id' => 1,
                'user_id' => 5,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],
            [   
                'staffer_id' => 5,
                'company_id' => 1,
                'user_id' => 6,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],
            [   
                'staffer_id' => 6,
                'company_id' => 1,
                'user_id' => 9,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],
            [   
                'staffer_id' => 7,
                'company_id' => 1,
                'user_id' => 10,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],
            [   
                'staffer_id' => 8,
                'company_id' => 1,
                'user_id' => 12,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],
            [   
                'staffer_id' => 9,
                'company_id' => 1,
                'user_id' => 13,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],
            [   
                'staffer_id' => 10,
                'company_id' => 1,
                'user_id' => 7,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],
            [   
                'staffer_id' => 11,
                'company_id' => 2,
                'user_id' => 14,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
                'dismissal_desc' => null,
            ],

        ]);
    }
}
