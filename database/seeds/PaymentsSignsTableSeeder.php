<?php

use Illuminate\Database\Seeder;

use App\PaymentsSign;

class PaymentsSignsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        PaymentsSign::insert([
            [
                'name' => 'Приход',
                'alias' => 'sell',
            ],
            [
                'name' => 'Расход',
                'alias' => 'buy',
            ],
            [
                'name' => 'Возврат прихода',
                'alias' => 'sellReturn',
            ],
            [
                'name' => 'Возврат расхода',
                'alias' => 'buyReturn',
            ],
        ]);
    }
}
