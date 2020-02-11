<?php

use Illuminate\Database\Seeder;

use App\PaymentsType;

class PaymentsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        PaymentsType::insert([
            [
                'name' => 'Наличный',
            ],
            [
                'name' => 'Безналичный',
            ],

        ]);
    }
}
