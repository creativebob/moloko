<?php

use Illuminate\Database\Seeder;

class ProductsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products_categories')->insert([
       [
        'company_id' => 1,
        'name' => 'Ворота',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Откатные',
        'parent_id' => 1,
        'category_status' => null,
        'author_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Секционные',
        'parent_id' => 1,
        'category_status' => null,
        'author_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Заборы',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
      ],
      [
        'company_id' => 1,
        'name' => 'Шлагбаумы',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
      ],
  ]);
    }
}
