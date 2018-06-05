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
        'category_id' => null,
      ],
      [
        'company_id' => 1,
        'name' => 'Откатные',
        'parent_id' => 1,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Секционные',
        'parent_id' => 1,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Заборы',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => null,
      ],
      [
        'company_id' => 1,
        'name' => 'Шлагбаумы',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => null,
      ],
      [
        'company_id' => 1,
        'name' => 'Монтаж',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'products_type_id' => 2,
        'category_id' => null,
      ],
      [
        'company_id' => 1,
        'name' => 'Монтаж откатных ворот',
        'parent_id' => 6,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 2,
        'category_id' => 6,
      ],
      [
        'company_id' => 1,
        'name' => 'Монтаж секционных ворот',
        'parent_id' => 6,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 2,
        'category_id' => 6,
      ],
    ]);
  }
}
