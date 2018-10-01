<?php

use Illuminate\Database\Seeder;

class LeadMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lead_methods')->insert([
        	[
		        'name' => 'Звонок',
                'author_id' => 1,
        	],
            [
                'name' => 'Обращение с сайта',
                'author_id' => 1,
            ],
            [
                'name' => 'Посещение офиса',
                'author_id' => 1,
            ],
            [
                'name' => 'Активное привлечение',
                'author_id' => 1,
            ],
            [
                'name' => 'Электронная почта',
                'author_id' => 1,
            ],
            [
                'name' => 'Обращение через мессенджер',
                'author_id' => 1,
            ],
            [
                'name' => 'Неформальное обращение через сотрудника компании',
                'author_id' => 1,
            ],
        ]);
    }
}
