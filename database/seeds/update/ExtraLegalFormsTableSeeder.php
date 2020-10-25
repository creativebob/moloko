<?php

use Illuminate\Database\Seeder;

class ExtraLegalFormsTableSeeder extends Seeder
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
                'name' => 'ОГУЭП',
                'full_name' => 'Областное государственное унитарное энергетическое предприятие',
                'description' => null,
                'sort' => 13,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ОГБУСО',
                'full_name' => 'Областное государственное бюджетное учреждение социального обслуживания',
                'description' => null,
                'sort' => 14,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'Фонд',
                'full_name' => 'Фонд',
                'description' => null,
                'sort' => 15,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'БФ',
                'full_name' => 'Благотворительный фонд',
                'description' => null,
                'sort' => 16,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ОГАУК',
                'full_name' => 'Областное государственное автономное учреждение культуры',
                'description' => null,
                'sort' => 17,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ГБУЗ',
                'full_name' => 'Государственное бюджетное учреждение здравоохранения',
                'description' => null,
                'sort' => 18,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ОГАУЗ',
                'full_name' => 'Областное государственное автономное учреждение здравоохранения',
                'description' => null,
                'sort' => 19,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ОД',
                'full_name' => 'Общественное движение',
                'description' => null,
                'sort' => 20,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'АНО ДПО',
                'full_name' => 'Автономная некоммерческая организация дополнительного профессионального образования',
                'description' => null,
                'sort' => 21,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'АНО ДО',
                'full_name' => 'Автономная некоммерческая организация дополнительного образования',
                'description' => null,
                'sort' => 22,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'МО',
                'full_name' => 'Муниципальное образование',
                'description' => null,
                'sort' => 23,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ОГКУ',
                'full_name' => 'Областное государственное казенное учреждение',
                'description' => null,
                'sort' => 24,
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'ОГКУЗ',
                'full_name' => 'Областное государственное казенное учреждение здравоохранения',
                'description' => null,
                'sort' => 25,
                'author_id' => 1,
            ],
        ]);
    }
}
