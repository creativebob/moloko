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
        		'company_id' => 1,
		        'user_id' => 4,
                'position_id' => 1,
		        'department_id' => 1,
        	],
        	[	
        		'company_id' => 1,
        		'user_id' => null,
                'position_id' => 2,
		        'department_id' => 3,
        	],
        	[	
        		'company_id' => 1,
        		'user_id' => null,
                'position_id' => 2,
		        'department_id' => 3,
        	],
        ]);
    }
}
