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
		        'name' => 'Иркутский филиал',
		        'phone' => 83952717775,
                'parent_id' => null,
                'filial_id' => null,
		        'filial_status' => 1,
                'location_id' => 5,
		        'company_id' => 1,
        	],
            [
                'name' => 'Отдел продаж',
                'phone' => null,
                'parent_id' => 1,
                'filial_id' => 1,
                'filial_status' => null,
                'location_id' => null,
                'company_id' => 1,
            ],
            [
                'name' => 'Отдел маркетинга и автоматизации',
                'phone' => null,
                'parent_id' => 1,
                'filial_id' => 1,
                'filial_status' => null,
                'location_id' => null,
                'company_id' => 1,
            ],
            [
                'name' => 'Улан-Удэнский филиал',
                'phone' => 83012613227,
                'parent_id' => null,
                'filial_id' => null,
                'filial_status' => 1,
                'location_id' => 6,
                'company_id' => 1,
            ],
            [
                'name' => 'Отдел продаж',
                'phone' => null,
                'parent_id' => 4,
                'filial_id' => 4,
                'filial_status' => null,
                'location_id' => null,
                'company_id' => 1,
            ],
            [
                'name' => 'Иркутский филиал',
                'phone' => 89015432289,
                'parent_id' => null,
                'filial_id' => null,
                'filial_status' => 1,
                'location_id' => 8,
                'company_id' => 2,
            ],
            [
                'name' => 'Отдел продаж',
                'phone' => null,
                'parent_id' => 6,
                'filial_id' => 6,
                'filial_status' => null,
                'location_id' => null,
                'company_id' => 2,
            ],
        ]);
    }
}
