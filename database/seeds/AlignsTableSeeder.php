<?php

use Illuminate\Database\Seeder;

use App\Align;

class AlignsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Align::insert([
            [
                'name' => 'Вверх',
                'tag' => 'top',
            ],
            [
                'name' => 'Лево',
                'tag' => 'left',
            ],
            [
                'name' => 'Центр',
                'tag' => 'center',
            ],
            [
                'name' => 'Право',
                'tag' => 'right',
            ],
            [
                'name' => 'Низ',
                'tag' => 'bottom',
            ],
        ]);
    }
}
