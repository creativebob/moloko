<?php

use Illuminate\Database\Seeder;

class DatabaseGeneralSeeder extends Seeder
{

    public function run()
    {

        // Кампания со штатом и расписанием
        $this->call(NetworksSocialTableSeeder::class);


    }

}
