<?php

use Illuminate\Database\Seeder;

class SourcesTableSeeder extends Seeder
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
		        'name' => 'Яндекс',
		        'description' => null,
                'domain' => 'yandex.ru',
                'utm' => 'yandex',
                'author_id' => 1,
        	],
            [
                'name' => 'Гугл',
                'description' => null,
                'domain' => 'google.com',
                'utm' => 'google',
                'author_id' => 1,
            ],
            [
                'name' => 'Мэйл',
                'description' => null,
                'domain' => 'mail.ru',
                'utm' => 'mail',
                'author_id' => 1,
            ],
            [
                'name' => 'Вконтакте',
                'description' => null,
                'domain' => 'vk.com',
                'utm' => 'vk',
                'author_id' => 1,
            ],
            [
                'name' => 'Фэйсбук',
                'description' => null,
                'domain' => 'facebook.com',
                'utm' => 'facebook',
                'author_id' => 1,
            ],
            [
                'name' => 'Инстаграм',
                'description' => null,
                'domain' => 'instagram.com',
                'utm' => 'instagram',
                'author_id' => 1,
            ],
            [
                'name' => 'Одноклассники',
                'description' => null,
                'domain' => 'ok.ru',
                'utm' => 'ok',
                'author_id' => 1,
            ],
            [
                'name' => '2GIS',
                'description' => null,
                'domain' => '2gis.ru',
                'utm' => '2gis',
                'author_id' => 1,
            ],
            [
                'name' => 'Авито',
                'description' => null,
                'domain' => 'avito.ru',
                'utm' => 'avito',
                'author_id' => 1,
            ],
        ]);
    }
}
