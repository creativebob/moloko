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

      // Шторка
      [
        'company_id' => 4,
        'name' => 'Портьерные ткани',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => null,
      ],

      [
        'company_id' => 4,
        'name' => 'Блекаут',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],

      [
        'company_id' => 4,
        'name' => 'Жаккард',
        'parent_id' => 10,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],
      [
        'company_id' => 4,
        'name' => 'Печать',
        'parent_id' => 10,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],
      [
        'company_id' => 4,
        'name' => 'Однотонный',
        'parent_id' => 10,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],

      [
        'company_id' => 4,
        'name' => 'Жаккард',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],

      [
        'company_id' => 4,
        'name' => 'Тафта',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],

      [
        'company_id' => 4,
        'name' => 'Однотонные',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],

      [
        'company_id' => 4,
        'name' => 'Канвас',
        'parent_id' => 16,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],
      [
        'company_id' => 4,
        'name' => 'Бархат',
        'parent_id' => 16,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],
      [
        'company_id' => 4,
        'name' => 'Софт',
        'parent_id' => 16,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],

      [
        'company_id' => 4,
        'name' => 'Лен',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],

      [
        'company_id' => 4,
        'name' => 'Однотонный',
        'parent_id' => 20,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],
      [
        'company_id' => 4,
        'name' => 'Жаккард',
        'parent_id' => 20,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],
      [
        'company_id' => 4,
        'name' => 'Печать',
        'parent_id' => 20,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],
      [
        'company_id' => 4,
        'name' => 'Вышивка',
        'parent_id' => 20,
        'category_status' => null,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => 9,
      ],

      [
        'company_id' => 4,
        'name' => 'Тюль',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'products_type_id' => 1,
        'category_id' => null,
      ],

    ]);
  }
}
