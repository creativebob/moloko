<?php

use Illuminate\Database\Seeder;

use App\EstimatesCancelGround;

class EstimatesCancelGroundsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        EstimatesCancelGround::insert([
            [
                'name' => 'Отказ клиента'
            ],
            [
                'name' => 'Ошибка менеджера'
            ],
        ]);
    }
}
