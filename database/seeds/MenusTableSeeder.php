<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
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
        'name' => 'ЦУП',
        'icon' => 'icon-mcc',
        'alias' => null,
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Тест для сотрудников',
        'icon' => 'icon-sale',
        'alias' => null,
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Маркетинг',
        'icon' => 'icon-marketing',
        'alias' => null,
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Справочники',
        'icon' => 'icon-guide',
        'alias' => null,
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Настройки',
        'icon' => 'icon-settings',
        'alias' => null,
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Компании',
        'icon' => null,
        'alias' => 'companies',
        'parent_id' => 1,
        'page_id' => 1,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Пользователи',
        'icon' => null,
        'alias' => 'users',
        'parent_id' => 1,
        'page_id' => 2,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Филиалы и отделы',
        'icon' => null,
        'alias' => 'departments',
        'parent_id' => 1,
        'page_id' => 3,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Штат',
        'icon' => null,
        'alias' => 'staff',
        'parent_id' => 1,
        'page_id' => 4,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Сотрудники',
        'icon' => null,
        'alias' => 'employees',
        'parent_id' => 1,
        'page_id' => 5,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Тестовая',
        'icon' => null,
        'alias' => 'home',
        'parent_id' => 2,
        'page_id' => 6,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Сайты',
        'icon' => null,
        'alias' => 'sites',
        'parent_id' => 3,
        'page_id' => 7,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Населенные пункты',
        'icon' => null,
        'alias' => 'cities',
        'parent_id' => 4,
        'page_id' => 8,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Должности',
        'icon' => null,
        'alias' => 'positions',
        'parent_id' => 4,
        'page_id' => 9,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Сущности',
        'icon' => null,
        'alias' => 'entities',
        'parent_id' => 5,
        'page_id' => 10,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Роли',
        'icon' => null,
        'alias' => 'roles',
        'parent_id' => 5,
        'page_id' => 11,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Права',
        'icon' => null,
        'alias' => 'rights',
        'parent_id' => 5,
        'page_id' => 12,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Страницы',
        'icon' => null,
        'alias' => 'pages',
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 1,
        'company_id' => null,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Навигации',
        'icon' => null,
        'alias' => 'navigations',
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 1,
        'company_id' => null,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Новости',
        'icon' => null,
        'alias' => 'news',
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 1,
        'company_id' => null,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Галерея',
        'icon' => null,
        'alias' => 'gallery',
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 1,
        'company_id' => null,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Списки',
        'icon' => null,
        'alias' => 'booklists',
        'parent_id' => 4,
        'page_id' => 16,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Секторы',
        'icon' => null,
        'alias' => 'sectors',
        'parent_id' => 4,
        'page_id' => 17,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Папки',
        'icon' => null,
        'alias' => 'folders',
        'parent_id' => 4,
        'page_id' => 18,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
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
        'display' => 1,
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
    [
        'name' => 'Альбомы',
        'icon' => null,
        'alias' => 'albums',
        'parent_id' => 3,
        'page_id' => 36,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Категории альбомов',
        'icon' => null,
        'alias' => 'albums_categories',
        'parent_id' => 4,
        'page_id' => 37,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Продукция',
        'icon' => 'icon-production',
        'alias' => null,
        'parent_id' => null,
        'page_id' => null,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Продукция',
        'icon' => null,
        'alias' => 'products',
        'parent_id' => 52,
        'page_id' => 41,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Категории продукции',
        'icon' => null,
        'alias' => 'products_categories',
        'parent_id' => 4,
        'page_id' => 40,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    // Шторка
    [
        'name' => 'Товары',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 42,
        'navigation_id' => 7,
        'company_id' => 4,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 2,
    ],
    [
        'name' => 'Услуги',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 43,
        'navigation_id' => 7,
        'company_id' => 4,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 3,
    ],
    [
        'name' => 'Контакты',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 44,
        'navigation_id' => 7,
        'company_id' => 4,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 4,
    ],
    [
        'name' => 'Студия',
        'icon' => null,
        'alias' => null,
        'parent_id' => null,
        'page_id' => 46,
        'navigation_id' => 7,
        'company_id' => 4,
        'system_item' => null,
        'author_id' => 1,
        'display' => 1,
        'sort' => 1,
    ],
]);
}
}
