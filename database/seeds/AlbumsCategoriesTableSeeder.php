<?php

use Illuminate\Database\Seeder;

class AlbumsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('albums_categories')->insert([
       [
        'company_id' => 1,
        'name' => 'Портфолио',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Товары',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Готовые обьекты',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Новости',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Жизнь компании',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
      ],

    ]);
    }
  }
