<?php

use Illuminate\Database\Seeder;
use App\Channel;

class ChannelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Channel::insert([
            [
                'name' => 'Telegram'
            ],
            [
                'name' => 'Email'
            ],
            [
                'name' => 'Смс'
            ],
        ]);
    }
}
