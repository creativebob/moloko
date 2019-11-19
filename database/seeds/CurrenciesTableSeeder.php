<?php

use Illuminate\Database\Seeder;
use App\Currency;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::insert([
           [
                'name' => 'Рубль',
                'abbreviation' => 'руб.',
                'symbol' => '₽'
           ],
            [
                'name' => 'Доллар',
                'abbreviation' => 'дол.',
                'symbol' => '$'
            ],
            [
                'name' => 'Юань',
                'abbreviation' => 'юан.',
                'symbol' => '¥'
            ],
            [
                'name' => 'Гривна',
                'abbreviation' => 'грив.',
                'symbol' => '₴'
            ],
        ]);
    }
}
