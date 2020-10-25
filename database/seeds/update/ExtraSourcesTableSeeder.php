<?php

use Illuminate\Database\Seeder;

class ExtraSourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sources')->insert([
            [
                'name' => 'Рамблер',
                'description' => null,
                'domain' => 'rambler.ru',
                'utm' => 'rambler',
                'author_id' => 1,
            ],
            [
                'name' => 'Pinterest',
                'description' => null,
                'domain' => 'pinterest.ru',
                'utm' => 'pinterest',
                'author_id' => 1,
            ],
            [
                'name' => 'Фламп',
                'description' => 'Отзывы о компаниях вашего города',
                'domain' => 'flamp.ru',
                'utm' => 'flamp',
                'author_id' => 1,
            ],
            [
                'name' => 'MyTarget',
                'description' => 'Отзывы о компаниях вашего города',
                'domain' => 'target.my.com',
                'utm' => 'mytarget',
                'author_id' => 1,
            ],
            [
                'name' => 'YouTube',
                'description' => 'Сервис видеохостинга',
                'domain' => 'youtube.com',
                'utm' => 'youtube',
                'author_id' => 1,
            ],
            [
                'name' => 'Email рассылка',
                'description' => 'Рассылка писем на электронную почту',
                'domain' => '',
                'utm' => 'letter',
                'author_id' => 1,
            ],
        ]);
    }
}
