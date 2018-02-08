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
		        'department_phone' => 89015432288,
                'department_parent_id' => null,
                'filial_id' => null,
		        'filial_status' => 1,
                'city_id' => 1,
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
            [
                'department_name' => 'Улан-Удэнский филиал',
                'department_address' => 'ул. Ленина, 45б',
                'department_phone' => 89115434288,
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
                'department_parent_id' => 4,
                'filial_id' => 4,
                'filial_status' => null,
                'city_id' => null,
                'company_id' => 1,
            ],
             [
                'department_name' => 'Красноярский филиал',
                'department_address' => 'ул. Мира, 11',
                'department_phone' => 89115434289,
                'department_parent_id' => null,
                'filial_id' => null,
                'filial_status' => 1,
                'city_id' => 3,
                'company_id' => 1,
            ],
            [
                'department_name' => 'Отдел продаж',
                'department_address' => null,
                'department_phone' => null,
                'department_parent_id' => 6,
                'filial_id' => 6,
                'filial_status' => null,
                'city_id' => null,
                'company_id' => 1,
            ],
        ]);
    }
}
