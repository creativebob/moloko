<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    		$this->call(CategotyRightTableSeeder::class);
    		$this->call(RightsTableSeeder::class);
    		$this->call(AccessGroupsTableSeeder::class);
    		$this->call(UsersTableSeeder::class);
    		$this->call(AccessesTableSeeder::class);
    }
}
