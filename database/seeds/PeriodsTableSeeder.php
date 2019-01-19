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
                'system_item' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Квартал',
                'tag' => 'quarter',
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Месяц',
                'tag' => 'month',
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Неделя',
                'tag' => 'week',
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'День',
                'tag' => 'day',
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Час',
                'tag' => 'hour',
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Минута',
                'tag' => 'minute',
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
            [
                'name' => 'Секунда',
                'tag' => 'second',
                'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
            ],
        ]);
    }
}

