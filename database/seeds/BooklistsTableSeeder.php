<?php

use Illuminate\Database\Seeder;

class BooklistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('booklists')->insert([
            [
                'booklists_name' => 'Тестовый список',
                'booklists_description' => 'Несколько людей из базы данных',
                'entity_id' => 1,
                'company_id' => 1,
                'author_id' => 1,
            ],
        ]);
    }
}
