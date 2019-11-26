<?php

use Illuminate\Database\Seeder;

class NotificationSiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notification_site')->insert([
            ['notification_id' => 1, 'site_id' => 1],
            ['notification_id' => 2, 'site_id' => 1],

        ]);
    }
}
