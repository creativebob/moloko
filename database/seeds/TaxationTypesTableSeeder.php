<?php

use Illuminate\Database\Seeder;

use App\TaxationType;

class TaxationTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaxationType::insert([
            [
                'name' => 'Общая',
                'alias' => 'osn',
            ],
            [
                'name' => 'Упрощенная (Доход)',
                'alias' => 'usn_income',
            ],
            [
                'name' => 'Упрощенная (Доход минус Расход)',
                'alias' => 'usn_income_outcome',
            ],
            [
                'name' => 'Единый налог на вмененный доход',
                'alias' => 'envd',
            ],
            [
                'name' => 'Единый сельскохозяйственный налог',
                'alias' => 'esn',
            ],
            [
                'name' => 'Патентная система налогообложения',
                'alias' => 'patent',
            ],
        ]);
    }
}
