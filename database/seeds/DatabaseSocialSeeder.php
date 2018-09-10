<?php

use Illuminate\Database\Seeder;

class DatabaseSocialSeeder extends Seeder
{

    public function run()
    {

        // Кампания со штатом и расписанием
        $this->call(NetworksSocialTableSeeder::class);


    }

}
