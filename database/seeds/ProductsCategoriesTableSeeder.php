<?php

use Illuminate\Database\Seeder;

class ProductsCategoriesTableSeeder extends Seeder
{
  public function run()
  {
    DB::table('products_categories')->insert([
      [
        'company_id' => 1,
        'name' => 'Ворота',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'products_type_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Откатные',
        'parent_id' => 1,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Секционные',
        'parent_id' => 1,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Заборы',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'products_type_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Шлагбаумы',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'products_type_id' => 1,
      ],
      [
        'company_id' => 4,
        'name' => 'Портьерные ткани',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'products_type_id' => 1,
      ],
      [
        'company_id' => 4,
        'name' => 'Блекаут',
        'parent_id' => 6,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
      ],
    ]);
  }
}
