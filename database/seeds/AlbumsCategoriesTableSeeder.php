<?php

use Illuminate\Database\Seeder;

class AlbumsCategoriesTableSeeder extends Seeder
{

  public function run()
  {
    DB::table('albums_categories')->insert([
      [
        'company_id' => null,
        'name' => 'Системные альбомы',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'system_item' => 1,
      ],
      [
        'company_id' => null,
        'name' => 'Общая',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'system_item' => null,
      ],
     
    ]);
  }
}
