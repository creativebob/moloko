<?php

use Illuminate\Database\Seeder,
    App\PaymentsMethod;

class PaymentsMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentsMethod::insert([
            [
                'sign' => 1,
                'name' => 'ПРЕДОПЛАТА 100%',
                'description' => 'Полная предварительная оплата до момента передачи предмета расчета',
                'alias' => 'full_prepayment',
            ],
            [
                'sign' => 2,
                'name' => 'ПРЕДОПЛАТА',
                'description' => 'Частичная предварительная оплата до момента передачи предмета расчета',
                'alias' => 'partial_prepayment',
            ],
            [
                'sign' => 3,
                'name' => 'АВАНС',
                'description' => 'Аванс',
                'alias' => 'advance',
            ],
            [
                'sign' => 4,
                'name' => 'ПОЛНЫЙ РАСЧЕТ',
                'description' => 'Полная оплата, в том числе с учетом аванса (предварительной оплаты) в момент передачи предмета расчета',
                'alias' => 'full_payment',
            ],
            [
                'sign' => 5,
                'name' => 'ЧАСТИЧНЫЙ РАСЧЕТ И КРЕДИТ',
                'description' => 'Частичная оплата предмета расчета в момент его передачи с последующей оплатой в кредит',
                'alias' => 'partial_payment',
            ],
            [
                'sign' => 6,
                'name' => 'ПЕРЕДАЧА В КРЕДИТ',
                'description' => 'Передача предмета расчета без его оплаты в момент его передачи с последующей оплатой в кредит',
                'alias' => 'credit',
            ],
            [
                'sign' => 7,
                'name' => 'ОПЛАТА КРЕДИТА',
                'description' => 'Оплата предмета расчета без его оплаты в момент его передачи с последующей оплатой в кредит (оплата кредита)',
                'alias' => 'credit_payment',
            ],
        ]);
    }
}
