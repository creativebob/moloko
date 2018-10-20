<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MessengersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('messengers')->insert([
    		[
    			'name' => 'Viber',
    			'icon' => 'icon-viber',
    			'author_id' => 1,
    			'created_at' => Carbon::now(),
    		],
    		[
    			'name' => 'WhatsApp',
    			'icon' => 'icon-whatsapp',
    			'author_id' => 1,
    			'created_at' => Carbon::now(),
    		],
    		[
    			'name' => 'Telegram',
    			'icon' => 'icon-telegram',
    			'author_id' => 1,
    			'created_at' => Carbon::now(),
    		],
    		[
    			'name' => 'Skype',
    			'icon' => 'icon-skype',
    			'author_id' => 1,
    			'created_at' => Carbon::now(),
    		],
    	]);
    }
}
