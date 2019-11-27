<?php

use Illuminate\Database\Seeder;
use App\Notification;
use App\Channel;
use App\Trigger;

class NotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $channels = Channel::get();
        $triggers = Trigger::get();

        Notification::insert([
    		[
    			'name' => 'Лид с сайта',
                'channel_id' => $channels->firstWhere('name', 'Telegram')->id,
                'trigger_id' => $triggers->firstWhere('alias', 'create-lead-from-project')->id,
    		],
            [
                'name' => 'Рекламация',
                'channel_id' => $channels->firstWhere('name', 'Telegram')->id,
                'trigger_id' => $triggers->firstWhere('alias', 'create-claim')->id,
            ],
    	]);
    }
}
