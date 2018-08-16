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
                'utm' => 'yandex',
                'author_id' => 1,
        	],
            [
                'name' => 'Гугл',
                'description' => null,
                'utm' => 'google',
                'author_id' => 1,
            ],
            [
                'name' => 'Мэйл',
                'description' => null,
                'utm' => 'mail',
                'author_id' => 1,
            ],
            [
                'name' => 'Вконтакте',
                'description' => null,
                'utm' => 'vk',
                'author_id' => 1,
            ],
            [
                'name' => 'Фэйсбук',
                'description' => null,
                'utm' => 'facebook',
                'author_id' => 1,
            ],
            [
                'name' => 'Инстаграм',
                'description' => null,
                'utm' => 'instagram',
                'author_id' => 1,
            ],
            [
                'name' => 'Одноклассники',
                'description' => null,
                'utm' => 'ok',
                'author_id' => 1,
            ],
            [
                'name' => '2GIS',
                'description' => null,
                'utm' => '2gis',
                'author_id' => 1,
            ],
            [
                'name' => 'Авито',
                'description' => null,
                'utm' => 'avito',
                'author_id' => 1,
            ],
        ]);
    }
}
