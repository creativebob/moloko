<?php

use Illuminate\Database\Seeder;

class PeriodsTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('periods')->insert([
            [
                'name' => 'Год',
                'tag' => 'year',
                'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
            ],
            [
                'name' => 'Квартал',
                'tag' => 'quarter',
                'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
            ],
            [
                'name' => 'Месяц',
                'tag' => 'month',
                'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
            ],
            [
                'name' => 'Неделя',
                'tag' => 'week',
                'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
            ],
            [
                'name' => 'День',
                'tag' => 'day',
                'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
            ],
            [
                'name' => 'Час',
                'tag' => 'hour',
                'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
            ],
            [
                'name' => 'Минута',
                'tag' => 'minute',
                'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
            ],
            [
                'name' => 'Секунда',
                'tag' => 'second',
                'company_id' => null,
                'author_id' => 1,
                'system' => true,
                'moderation' => false,
            ],
        ]);
    }
}

