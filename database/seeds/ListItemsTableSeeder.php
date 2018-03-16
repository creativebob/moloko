<?php

use Illuminate\Database\Seeder;

class ListItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('list_items')->insert([
            [
                'item_entity' => 5,
                'booklist_id' => 1,
            ],
            [
                'item_entity' => 6,
                'booklist_id' => 1,
            ],
            [
                'item_entity' => 7,
                'booklist_id' => 2,
            ],
            [
                'item_entity' => 8,
                'booklist_id' => 2,
            ],
            [
                'item_entity' => 9,
                'booklist_id' => 3,
            ],
        ]);
    }
}
