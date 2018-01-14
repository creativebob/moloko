<?php

use Illuminate\Database\Seeder;

class ListUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('list_user')->insert([
            [
                'booklist_id' => 1,
                'user_id' => 4,
            ],
        ]);
    }
}
