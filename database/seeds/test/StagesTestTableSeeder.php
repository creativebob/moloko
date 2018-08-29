<?php

use Illuminate\Database\Seeder;

class StagesTestTableSeeder extends Seeder
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
                'company_id' => 1,
            ],
            [
                'name' => 'Замер',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'В работе',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Готов',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Отзыв',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Перезвонить',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Отказ',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Расчет готов',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Подписание',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Договор подписан',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Запуск',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Закуп',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Производство',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Доставка',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Монтаж',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Тех Обслуживание',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Обращение',
                'description' => null,
                'author_id' => 1,
                'company_id' => 1,
            ],

        ]);
    }
}
