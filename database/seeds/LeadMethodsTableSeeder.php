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
                'mode' => 1,
                'author_id' => 1,
        	],
            [
                'name' => 'Обращение с сайта',
                'mode' => 3,
                'author_id' => 1,
            ],
            [
                'name' => 'Посещение офиса',
                'mode' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Активное привлечение',
                'mode' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Электронная почта',
                'mode' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Обращение через мессенджер',
                'mode' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Неформальное обращение через сотрудника компании',
                'mode' => 1,
                'author_id' => 1,
            ],
        ]);
    }
}
