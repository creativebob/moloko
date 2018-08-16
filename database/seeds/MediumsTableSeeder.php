<?php

use Illuminate\Database\Seeder;

class MediumsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mediums')->insert([
        	[
		        'name' => 'Контекстная реклама',
		        'description' => null,
                'utm' => 'cpc',
                'author_id' => 1,
        	],
            [
                'name' => 'Баннерная реклама',
                'description' => null,
                'utm' => 'banner',
                'author_id' => 1,
            ],
            [
                'name' => 'Социальные сети',
                'description' => null,
                'utm' => 'social',
                'author_id' => 1,
            ],
            [
                'name' => 'Товарные агрегаторы',
                'description' => null,
                'utm' => 'aggregator',
                'author_id' => 1,
            ],
            [
                'name' => 'Классифайды',
                'description' => null,
                'utm' => 'classifide',
                'author_id' => 1,
            ],
            [
                'name' => 'Email-маркетинг',
                'description' => null,
                'utm' => 'email',
                'author_id' => 1,
            ],
            [
                'name' => 'Телевидение',
                'description' => null,
                'utm' => 'tv',
                'author_id' => 1,
            ],
            [
                'name' => 'Радио',
                'description' => null,
                'utm' => 'radio',
                'author_id' => 1,
            ],
            [
                'name' => 'Наружная реклама',
                'description' => null,
                'utm' => 'outdoor',
                'author_id' => 1,
            ],
            [
                'name' => 'Реклама в прессе',
                'description' => null,
                'utm' => 'press',
                'author_id' => 1,
            ],
            [
                'name' => 'Стенды',
                'description' => null,
                'utm' => 'stand',
                'author_id' => 1,
            ],
            [
                'name' => 'Клубы',
                'description' => null,
                'utm' => 'club',
                'author_id' => 1,
            ],
            [
                'name' => 'Сарафанное радио',
                'description' => null,
                'utm' => 'wordofmouth',
                'author_id' => 1,
            ],
        ]);
    }
}
