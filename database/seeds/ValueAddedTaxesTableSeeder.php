<?php

use Illuminate\Database\Seeder;

use App\ValueAddedTax;

class ValueAddedTaxesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ValueAddedTax::insert([
            [
                'name' => 'Без НДС',
                'tag' => 'none',
                'description' => ''
            ],
            [
                'name' => 'НДС 0%',
                'tag' => 'vat0',
                'description' => ''
            ],
            [
                'name' => 'НДС 10%',
                'tag' => 'vat10',
                'description' => ''
            ],
            [
                'name' => 'НДС 10/110%',
                'tag' => 'vat110',
                'description' => ''
            ],
            [
                'name' => 'НДС 18%',
                'tag' => 'vat18',
                'description' => ''
            ],
            [
                'name' => 'НДС 18/118%',
                'tag' => 'vat118',
                'description' => ''
            ],
            [
                'name' => 'НДС 20%',
                'tag' => 'vat20',
                'description' => ''
            ],
            [
                'name' => 'НДС 20/120%',
                'tag' => 'vat120',
                'description' => ''
            ],
        ]);
    }
}