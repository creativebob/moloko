<?php

use Illuminate\Database\Seeder;

class MenusTestTableSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run()
  {
    DB::table('menus')->insert([
    [
        'name' => 'О компании',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 15,
        'navigation_id' => 3,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 1,
    ],
    [
        'name' => 'Новости',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 19,
        'navigation_id' => 3,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 2,
    ],
    [
        'name' => 'Контакты',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 20,
        'navigation_id' => 3,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 6,
    ],
    [
        'name' => 'Замер',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 21,
        'navigation_id' => 3,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 5,
    ],
    [
        'name' => 'Гаражные ворота',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 22,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 1,
    ],
    [
        'name' => 'Уличные ворота',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 23,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 2,
    ],
    [
        'name' => 'Рольставни',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 24,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 5,
    ],
    [
        'name' => 'Автоматика',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 25,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 6,
    ],
    [
        'name' => 'Стальные двери',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 26,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => null,
        'sort' => 8,
    ],
    [
        'name' => 'Перегрузочные системы',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 27,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 10,
    ],
    [
        'name' => 'Сервисный центр',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 28,
        'navigation_id' => 3,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 3,
    ],
    [
        'name' => 'База знаний',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 29,
        'navigation_id' => 3,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 4,
    ],
    [
        'name' => 'Заборы',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 30,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 3,
    ],
    [
        'name' => 'Ангары',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 31,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => null,
        'sort' => 4,
    ],
    [
        'name' => 'Шлагбаумы',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 32,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 7,
    ],
    [
        'name' => 'Противопожарные ворота',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 33,
        'navigation_id' => 4,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 9,
    ],
    [
        'name' => 'Подать заявку на замер',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 21,
        'navigation_id' => 5,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Наша команда',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 34,
        'navigation_id' => 5,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => null,
        'sort' => null,
    ],
    [
        'name' => 'Вакансии',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 35,
        'navigation_id' => 5,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => null,
        'sort' => null,
    ],
    [
        'name' => 'Сервисный центр',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 28,
        'navigation_id' => 5,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Instagram',
        'icon' => null,
        'alias' => 'https://instagram.com/vorotamars',
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 6,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Facebook',
        'icon' => null,
        'alias' => 'https://www.facebook.com/vorotamars',
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 6,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Вконтакте',
        'icon' => null,
        'alias' => 'https://vk.com/vorotamars',
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 6,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Одноклассники',
        'icon' => null,
        'alias' => 'https://ok.ru/vorotamars',
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 6,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Youtube',
        'icon' => null,
        'alias' => 'https://youtube.com/channel/UCTVWvgfC2wT-Po1HfFk',
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 6,
        'company_id' => 1,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
   

]);
}
}
