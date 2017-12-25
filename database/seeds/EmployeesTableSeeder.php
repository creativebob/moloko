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
        	],
            [   
                'staffer_id' => 2,
                'company_id' => 1,
                'user_id' => 5,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
            ],
            [   
                'staffer_id' => 3,
                'company_id' => 1,
                'user_id' => 6,
                'date_employment' => '2017-12-01',
                'date_dismissal' => null,
            ],
            [   
                'staffer_id' => 4,
                'company_id' => 1,
                'user_id' => 6,
                'date_employment' => '2017-11-01',
                'date_dismissal' => '2017-11-02',
            ],
        ]);
    }
}
