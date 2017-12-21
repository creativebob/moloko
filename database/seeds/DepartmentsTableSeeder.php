<?php

use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('departments')->insert([
        	[
		        'department_name' => 'Иркутский филиал',
                'department_address' => 'ул. Шевцова, 5',
		        'department_phone' => 8901543228877,
		        'filial_status' => 1,
		        'company_id' => 1,
        	],
        	[
		        'department_name' => 'Улан-Удэнский филиал',
                'department_address' => 'ул. Ленина, 45б',
		        'department_phone' => 8911543428877,
		        'filial_status' => 1,
		        'company_id' => 1,
        	],
        ]);
    }
}
