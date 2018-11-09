<?php

use Illuminate\Database\Seeder;

class LoyaltiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('loyalties')->insert([
            [
                'company_id' => null,
                'name' => 'Злопыхатель',
                'description' => 'Настроен враждебно. Не конструктивен. Не адекватен.',
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'Негатив',
                'description' => 'Дал понять, что отношение негативное. Готов оставить плохой отзыв. Готов судиться.',
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'Подпорченные отношения',
                'description' => 'Получил негативные эмоции от взаимодействия. Даже если не озвучил.',
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'Полет нормальный',
                'description' => 'Обычное взаимодействие',
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'Отлично',
                'description' => 'Выразил благодарность или признательность. Готов написать положительный отзыв.',
                'author_id' => 1,
            ],
            [
                'company_id' => null,
                'name' => 'Амбасадор',
                'description' => 'Фанат компании. Готов при случае делать рекламу и всячески хвалить.',
                'author_id' => 1,
            ],
        ]);
    }
}
