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
                'system' => true,
            ],
            [
                'name' => 'Общая',
                'author_id' => 1,
                'system' => false,
            ],
        ]);
    }
}
