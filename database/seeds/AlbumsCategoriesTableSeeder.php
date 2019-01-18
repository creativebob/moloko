<?php

use Illuminate\Database\Seeder;

use App\AlbumsCategory;

class AlbumsCategoriesTableSeeder extends Seeder
{

    public function run()
    {
        AlbumsCategory::insert([
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
