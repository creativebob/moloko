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
                'address' => 'ул. Шевцова, 5, оф. 202',
		        'phone' => 83952717775,
                'department_parent_id' => null,
                'filial_id' => null,
		        'filial_status' => 1,
                'city_id' => 1,
		        'company_id' => 1,
        	],
            [
                'department_name' => 'Отдел продаж',
                'address' => null,
                'phone' => null,
                'department_parent_id' => 1,
                'filial_id' => 1,
                'filial_status' => null,
                'city_id' => null,
                'company_id' => 1,
            ],
            [
                'department_name' => 'Отдел маркетинга и автоматизации',
                'address' => null,
                'phone' => null,
                'department_parent_id' => 1,
                'filial_id' => 1,
                'filial_status' => null,
                'city_id' => null,
                'company_id' => 1,
            ],
            [
                'department_name' => 'Улан-Удэнский филиал',
                'address' => 'ул. Ленина, 45б',
                'phone' => 89115434288,
                'department_parent_id' => null,
                'filial_id' => null,
                'filial_status' => 1,
                'city_id' => 2,
                'company_id' => 1,
            ],
            [
                'department_name' => 'Отдел продаж',
                'address' => null,
                'phone' => null,
                'department_parent_id' => 4,
                'filial_id' => 4,
                'filial_status' => null,
                'city_id' => null,
                'company_id' => 1,
            ],
             [
                'department_name' => 'Читинский филиал',
                'address' => 'ул. Мира, 11',
                'phone' => 89115434289,
                'department_parent_id' => null,
                'filial_id' => null,
                'filial_status' => 1,
                'city_id' => 4,
                'company_id' => 1,
            ],
            [
                'department_name' => 'Отдел продаж',
                'address' => null,
                'phone' => null,
                'department_parent_id' => 6,
                'filial_id' => 6,
                'filial_status' => null,
                'city_id' => null,
                'company_id' => 1,
            ],
                        [
                'department_name' => 'Иркутский филиал',
                'address' => 'ул. Шевцова, 6',
                'phone' => 89015432289,
                'department_parent_id' => null,
                'filial_id' => null,
                'filial_status' => 1,
                'city_id' => 1,
                'company_id' => 2,
            ],
            [
                'department_name' => 'Отдел продаж',
                'address' => null,
                'phone' => null,
                'department_parent_id' => 8,
                'filial_id' => 8,
                'filial_status' => null,
                'city_id' => null,
                'company_id' => 2,
            ],
        ]);
    }
}
