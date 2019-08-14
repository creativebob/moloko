<?php

use Illuminate\Database\Seeder;

class StagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stages')->insert([
        	[
                'name' => 'Потенциальный',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Обращение',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Сбор информации',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Расчет стоимости',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Подписание',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Оплата',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Закуп',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Подготовка к производству',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Производство',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Доставка',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Монтаж',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Готов',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Отказ',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            [
                'name' => 'Обслуживание',
                'description' => null,
                'author_id' => 1,
                'display' => true,
                'company_id' => null,
            ],
            

        ]);
    }
}
