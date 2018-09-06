<?php

use Illuminate\Database\Seeder;

class NotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('notifications')->insert([
    		[
    			'name' => 'Лид с сайта',
    		],
    	]);
    }
}
