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
                'name' => 'Тестовый список',
                'description' => 'Несколько людей из базы данных',
                'entity_id' => 1,
                'entity_alias' => 'users',
                'company_id' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Списочек',
                'description' => 'Несколько людей',
                'entity_id' => 1,
                'entity_alias' => 'users',
                'company_id' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Еще один лишеный надежды список',
                'description' => 'Один чел - изгой',
                'entity_id' => 1,
                'entity_alias' => 'users',
                'company_id' => 1,
                'author_id' => 1,
            ],
        ]);
    }
}
