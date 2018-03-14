<?php

use Illuminate\Database\Seeder;

class BooklistUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('booklist_user')->insert([
            [
                'booklist_id' => 1,
                'user_id' => 4,
            ],
            [
                'booklist_id' => 2,
                'user_id' => 4,
            ],
            [
                'booklist_id' => 3,
                'user_id' => 4,
            ],
        ]);
    }
}
