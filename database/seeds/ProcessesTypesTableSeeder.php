<?php

use Illuminate\Database\Seeder;

class ProcessesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('processes_types')->insert([
        	[
		        'name' => 'Воздействие на тело человека',
		        'description' => 'Услуги, направленные на тело человека',
                'author_id' => 1,
                'system_item' => 1,
        	],
        	[
		        'name' => 'Воздействие на материальный объект',
		        'description' => 'Услуги, направленные на физические объекты, находящиеся в собственности человека',
                'author_id' => 1,
                'system_item' => 1,
        	],
        	[
		        'name' => 'Воздействие на сознание человека',
		        'description' => 'Услуги, направленные на сознание человека',
                'author_id' => 1,
                'system_item' => 1,
        	],
        	[
		        'name' => 'Нематериальный актив',
		        'description' => 'Услуги, направленные на нематериальные актив',
                'author_id' => 1,
                'system_item' => 1,
        	],
        ]);
    }
}
