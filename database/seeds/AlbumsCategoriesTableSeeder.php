<?php

use Illuminate\Database\Seeder;

use App\AlbumsCategories;

class AlbumsCategoriesTableSeeder extends Seeder
{

    public function run()
    {
        AlbumsCategories::insert([
            [
                'name' => 'Системные альбомы',
                'author_id' => 1,
                'system_item' => 1,
            ],
            [
                'name' => 'Общая',
                'author_id' => 1,
                'system_item' => null,
            ],
        ]);
    }
}
