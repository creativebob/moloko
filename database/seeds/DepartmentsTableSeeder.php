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
                'department_parent_id' => null,
                'filial_id' => null,
		        'filial_status' => 1,
                'city_id' => 1,
		        'company_id' => 1,
        	],
        	[
		        'department_name' => 'Улан-Удэнский филиал',
                'department_address' => 'ул. Ленина, 45б',
		        'department_phone' => 8911543428877,
                'department_parent_id' => null,
                'filial_id' => null,
                'filial_status' => 1,
                'city_id' => 2,
		        'company_id' => 1,
        	],
            [
                'department_name' => 'Отдел продаж',
                'department_address' => null,
                'department_phone' => null,
                'department_parent_id' => 1,
                'filial_id' => 1,
                'filial_status' => null,
                'city_id' => null,
                'company_id' => 1,
            ],
             [
                'department_name' => 'Отдел маркетинга и автоматизации',
                'department_address' => null,
                'department_phone' => null,
                'department_parent_id' => 1,
                'filial_id' => 1,
                'filial_status' => null,
                'city_id' => null,
                'company_id' => 1,
            ],
        ]);
    }
}
