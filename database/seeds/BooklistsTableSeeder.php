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
                'booklist_name' => 'Тестовый список',
                'booklist_description' => 'Несколько людей из базы данных',
                'entity_id' => 1,
                'entity_alias' => 'users',
                'company_id' => 1,
                'author_id' => 1,
            ],
            [
                'booklist_name' => 'Списочек',
                'booklist_description' => 'Несколько людей',
                'entity_id' => 1,
                'entity_alias' => 'users',
                'company_id' => 1,
                'author_id' => 1,
            ],
            [
                'booklist_name' => 'Еще один лишеный надежды список',
                'booklist_description' => 'Один чел - изгой',
                'entity_id' => 1,
                'entity_alias' => 'users',
                'company_id' => 1,
                'author_id' => 1,
            ],
        ]);
    }
}
