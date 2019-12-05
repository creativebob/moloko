<?php

use Illuminate\Database\Seeder;
use App\DisplayMode;

class DisplayModesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DisplayMode::insert([
           [
               'name' => 'Блок',
               'alias' => 'box',
           ],
            [
                'name' => 'Список',
                'alias' => 'list',
            ],
        ]);
    }
}
