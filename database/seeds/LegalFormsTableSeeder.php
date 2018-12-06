<?php

use Illuminate\Database\Seeder;

class LegalFormsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('legal_forms')->insert([
            [
                'company_id' => null,
                'name' => 'ООО',
                'full_name' => 'Общество с ограниченной ответственностью',
                'description' => null,
                'sort' => 1,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ИП',
                'full_name' => 'Индивидуальный предприниматель',
                'description' => null,
                'sort' => 2,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ОАО',
                'full_name' => 'Открытое акционерное общество',
                'description' => null,
                'sort' => 3,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ПАО',
                'full_name' => 'Публичное акционерное общество',
                'description' => null,
                'sort' => 4,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'КФХ',
                'full_name' => 'Фермерское хозяйство',
                'description' => null,
                'sort' => 5,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ПК',
                'full_name' => 'Производственный кооператив',
                'description' => null,
                'sort' => 6,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ТСЖ',
                'full_name' => 'Товарищество собственников жилья',
                'description' => null,
                'sort' => 7,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ФГУП',
                'full_name' => 'Федеральное государственное унитарное предприятие',
                'description' => null,
                'sort' => 8,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'МУП',
                'full_name' => 'Муниципальное унитарное предприятие',
                'description' => null,
                'sort' => 9,
                'author_id' => 1,
            ],
        ]);
    }
}
