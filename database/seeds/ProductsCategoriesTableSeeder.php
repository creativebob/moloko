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
        
        'category_id' => null,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 1,
        'name' => 'Откатные',
        'parent_id' => 1,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 1,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 1,
        'name' => 'Секционные',
        'parent_id' => 1,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 1,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 1,

    ],
    [
        'company_id' => 1,
        'name' => 'Заборы',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        
        'category_id' => null,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 26,
        
        'status' => 'set',
        'products_mode_id' => 1,

    ],
    [
        'company_id' => 1,
        'name' => 'Шлагбаумы',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        
        'category_id' => null,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 1,

    ],
    [
        'company_id' => 1,
        'name' => 'Монтаж',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,

        'category_id' => null,
        'display' => 1,
        'type' => 'services',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 2,
    ],
    [
        'company_id' => 1,
        'name' => 'Монтаж откатных ворот',
        'parent_id' => 6,
        'category_status' => null,
        'author_id' => 1,

        'category_id' => 6,
        'display' => 1,
        'type' => 'services',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 2,
    ],
    [
        'company_id' => 1,
        'name' => 'Монтаж секционных ворот',
        'parent_id' => 6,
        'category_status' => null,
        'author_id' => 1,

        'category_id' => 6,
        'display' => 1,
        'type' => 'services',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 2,
    ],



      // Шторка
    [
        'company_id' => 4,
        'name' => 'Портьерные ткани',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        
        'category_id' => null,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Блекаут',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Жаккард',
        'parent_id' => 10,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Печать',
        'parent_id' => 10,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Однотонный',
        'parent_id' => 10,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Жаккард',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Тафта',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Однотонные',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Канвас',
        'parent_id' => 16,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Бархат',
        'parent_id' => 16,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Софт',
        'parent_id' => 16,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Лен',
        'parent_id' => 9,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Однотонный',
        'parent_id' => 20,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Жаккард',
        'parent_id' => 20,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Печать',
        'parent_id' => 20,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Вышивка',
        'parent_id' => 20,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 9,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Тюль',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        
        'category_id' => null,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

      // Общие
    [
        'company_id' => 1,
        'name' => 'Метал',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,

        'category_id' => null,
        'display' => 1,
        'type' => 'raws',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 5,
    ],
    [
        'company_id' => 1,
        'name' => 'Профлист',
        'parent_id' => 26,
        'category_status' => null,
        'author_id' => 1,

        'category_id' => 26,
        'display' => 1,
        'type' => 'raws',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 5,
    ],
    [
        'company_id' => 1,
        'name' => 'Песок',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,

        'category_id' => null,
        'display' => 1,
        'type' => 'raws',
        'unit_id' => 26,
        
        'status' => 'one',
        'products_mode_id' => 5,
    ],

      // Шторка
    [
        'company_id' => 4,
        'name' => 'Вуаль',
        'parent_id' => 25,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Однотонная',
        'parent_id' => 29,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Печать',
        'parent_id' => 29,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Кристалл',
        'parent_id' => 25,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Однотонный',
        'parent_id' => 32,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Печать',
        'parent_id' => 32,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Вышивка',
        'parent_id' => 32,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Органза',
        'parent_id' => 25,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Вышитая',
        'parent_id' => 36,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Деворе',
        'parent_id' => 36,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Полуорганза',
        'parent_id' => 25,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Однотонная',
        'parent_id' => 39,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Фентези',
        'parent_id' => 39,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Сетка',
        'parent_id' => 25,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Однотонная',
        'parent_id' => 42,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Вышитая',
        'parent_id' => 42,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Полоски',
        'parent_id' => 42,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Пьеза',
        'parent_id' => 42,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Фентези',
        'parent_id' => 42,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Лен',
        'parent_id' => 25,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Однотонный',
        'parent_id' => 48,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Печать',
        'parent_id' => 48,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Жаккардовый',
        'parent_id' => 48,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 25,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Ткани для столового белья',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        
        'category_id' => null,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Однотонный',
        'parent_id' => 52,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 52,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Жаккардовые',
        'parent_id' => 52,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 52,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Ткани для детской комнаты',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        
        'category_id' => null,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Тюль',
        'parent_id' => 55,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 55,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Портьера',
        'parent_id' => 55,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 55,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Домашний текстиль',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        
        'category_id' => null,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Полотенца',
        'parent_id' => 58,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 58,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Кпб',
        'parent_id' => 58,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 58,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Подушки',
        'parent_id' => 58,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 58,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Одеяла',
        'parent_id' => 58,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 58,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Халаты',
        'parent_id' => 58,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 58,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Текстиль для кухни',
        'parent_id' => 58,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 58,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],

    [
        'company_id' => 4,
        'name' => 'Аксессуары для штор',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        
        'category_id' => null,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Магнитные клипсыи',
        'parent_id' => 65,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 65,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Кисти',
        'parent_id' => 65,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 65,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Прихваты',
        'parent_id' => 65,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 65,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Люверсы',
        'parent_id' => 65,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 65,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],
    [
        'company_id' => 4,
        'name' => 'Зажимы',
        'parent_id' => 65,
        'category_status' => null,
        'author_id' => 1,
        
        'category_id' => 65,
        'display' => 1,
        'type' => 'goods',
        'unit_id' => 18,
        
        'status' => 'one',
        'products_mode_id' => 1,
    ],


]);
}
}
