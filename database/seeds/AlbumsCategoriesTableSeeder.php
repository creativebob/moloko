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
        'company_id' => 1,
        'name' => 'Портфолио',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'system_item' => null,
      ],
      [
        'company_id' => 1,
        'name' => 'Товары',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'system_item' => null,
      ],
      [
        'company_id' => 1,
        'name' => 'Готовые обьекты',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'system_item' => null,
      ],
      [
        'company_id' => 1,
        'name' => 'Новости',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'system_item' => null,
      ],
      [
        'company_id' => 1,
        'name' => 'Жизнь компании',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'system_item' => null,
      ],
    ]);
  }
}
