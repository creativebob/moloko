<?php

use Illuminate\Database\Seeder;

class LeadTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lead_types')->insert([
        	[
		        'name' => 'Обычное обращение',
                'author_id' => 1,
        	],
            [
                'name' => 'Дилерское обращение',
                'author_id' => 1,
            ],
            [
                'name' => 'Сервисное обращение',
                'author_id' => 1,
            ],
        ]);
    }
}
