<?php

use Illuminate\Database\Seeder;

class WidgetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('widgets')->insert([
        	[
		        'name' => 'Нагрузка на отдел продаж',
		        'description' => 'Статистика по задачам на менеджеров',
		        'tag' => 'sales-department-burden',       
                'author_id' => 1,
        	]

        ]);
    }
}
