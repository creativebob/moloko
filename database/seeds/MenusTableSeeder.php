<?php

use Illuminate\Database\Seeder;

use App\Menu;
use App\Page;

class MenusTableSeeder extends Seeder
{

    public function run()
    {

        // Разделы для сайта, котоыре имхо должны быть в другой таблице
        Menu::insert([
            [
                'name' => 'Страницы',
                'icon' => null,
                'alias' => 'pages',
                'tag' => 'pages',
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
                'tag' => 'navigations',
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
                'tag' => 'news',
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
                'name' => 'Каталог',
                'icon' => null,
                'alias' => 'catalogs',
                'tag' => 'catalogs',
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
                'name' => 'Продукция на сайте',
                'icon' => null,
                'alias' => 'catalog_products',
                'tag' => 'catalog_products',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system_item' => null,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],
        ]);

        // Категории в сайдбаре
        Menu::insert([

            // 1 ЦУП
            // [
            //     'name' => 'ЦУП',
            //     'icon' => 'icon-mcc',
            //     'alias' => 'admin/dashboard',
            //     'tag' => 'mcc',
            //     'parent_id' => null,
            //     'page_id' => 31,
            //     'navigation_id' => 2,
            //     'company_id' => null,
            //     'system_item' => 1,
            //     'author_id' => 1,
            //     'display' => 1,
            //     'sort' => 1,
            // ],

            // 1 Процессы
            [
                'name' => 'Процессы',
                'icon' => 'icon-process',
                'alias' => null,
                'tag' => 'processes',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 2,
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],

            // 2 Персонал
            [
                'name' => 'Персонал',
                'icon' => 'icon-personal',
                'alias' => null,
                'tag' => 'personals',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 2,
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],

            // 3 Продажи
            [
                'name' => 'Продажи',
                'icon' => 'icon-sale',
                'alias' => null,
                'tag' => 'sales',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 2,
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],

            // 4 Продукция
            [
                'name' => 'Продукция',
                'icon' => 'icon-product',
                'alias' => null,
                'tag' => 'products',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 2,
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],

            // 5 Финансы
            [
                'name' => 'Финансы',
                'icon' => 'icon-finance',
                'alias' => null,
                'tag' => 'finances',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 2,
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],

            // 6 Производство
            [
                'name' => 'Прoизводство',
                'icon' => 'icon-production',
                'alias' => null,
                'tag' => 'productions',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 2,
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],

            // 7 Маркетинг
            [
                'name' => 'Маркетинг',
                'icon' => 'icon-marketing',
                'alias' => null,
                'tag' => 'marketings',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 2,
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],

            // 8 Справочники
            [
                'name' => 'Справочники',
                'icon' => 'icon-guide',
                'alias' => null,
                'tag' => 'guides',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 2,
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],

            // 9 Настройки
            [
                'name' => 'Настройки',
                'icon' => 'icon-setting',
                'alias' => null,
                'tag' => 'settings',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 2,
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
                'sort' => null,
            ],
        ]);

$menus = Menu::where('navigation_id', 2)->get(['id', 'tag']);
$pages = Page::where('site_id', 1)->get(['id', 'alias']);

// Первый уровень вложенности
Menu::insert([

    // Процессы
    [
        'name' => 'Задачи',
        'icon' => null,
        'alias' => 'admin/challenges',
        'tag' => 'challenges',
        'parent_id' => $menus->where('tag', 'processes')->first()->id,
        'page_id' => $pages->where('alias', 'challenges')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    // Персонал
    [
        'name' => 'Пользователи',
        'icon' => null,
        'alias' => 'admin/users',
        'tag' => 'users',
        'parent_id' => $menus->where('tag', 'personals')->first()->id,
        'page_id' => $pages->where('alias', 'users')->first()->id,
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
        'alias' => 'admin/departments',
        'tag' => 'departments',
        'parent_id' => $menus->where('tag', 'personals')->first()->id,
        'page_id' => $pages->where('alias', 'departments')->first()->id,
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
        'alias' => 'admin/staff',
        'tag' => 'staff',
        'parent_id' => $menus->where('tag', 'personals')->first()->id,
        'page_id' => $pages->where('alias', 'staff')->first()->id,
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
        'alias' => 'admin/employees',
        'tag' => 'employees',
        'parent_id' => $menus->where('tag', 'personals')->first()->id,
        'page_id' => $pages->where('alias', 'employees')->first()->id,
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
        'alias' => 'admin/positions',
        'tag' => 'positions',
        'parent_id' => $menus->where('tag', 'personals')->first()->id,
        'page_id' => $pages->where('alias', 'positions')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    // Продажи
    [
        'name' => 'Заказы',
        'icon' => null,
        'alias' => 'admin/orders',
        'tag' => 'orders',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'orders')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Дилеры',
        'icon' => null,
        'alias' => 'admin/dealers',
        'tag' => 'dealers',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'dealers')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Расчеты',
        'icon' => null,
        'alias' => 'admin/estimates',
        'tag' => 'estimates',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'estimates')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Лиды',
        'icon' => null,
        'alias' => 'admin/leads',
        'tag' => 'leads',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'leads')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Клиенты',
        'icon' => null,
        'alias' => 'admin/clients',
        'tag' => 'clients',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'clients')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    // Продукция
    [
        'name' => 'Товары',
        'icon' => null,
        'alias' => 'admin/goods',
        'tag' => 'goods',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'goods')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Услуги',
        'icon' => null,
        'alias' => 'admin/services',
        'tag' => 'services',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'services')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Сырьё',
        'icon' => null,
        'alias' => 'admin/raws',
        'tag' => 'raws',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'raws')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    [
        'name' => 'Категории товаров',
        'icon' => null,
        'alias' => 'admin/goods_categories',
        'tag' => 'goods_categories',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'goods_categories')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Категории услуг',
        'icon' => null,
        'alias' => 'admin/services_categories',
        'tag' => 'services_categories',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'services_categories')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Категории сырья',
        'icon' => null,
        'alias' => 'admin/raws_categories',
        'tag' => 'raws_categories',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'raws_categories')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Категории расходных материалов',
        'icon' => null,
        'alias' => 'admin/expendables_categories',
        'tag' => 'expendables_categories',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'expendables_categories')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    [
        'name' => 'Группы товаров',
        'icon' => null,
        'alias' => 'admin/goods_products',
        'tag' => 'goods_products',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'goods_products')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Группы услуг',
        'icon' => null,
        'alias' => 'admin/services_products',
        'tag' => 'services_products',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'services_products')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Группы сырья',
        'icon' => null,
        'alias' => 'admin/raws_products',
        'tag' => 'raws_products',
        'parent_id' => $menus->where('tag', 'products')->first()->id,
        'page_id' => $pages->where('alias', 'raws_products')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    // Финансы
    [
        'name' => 'Банки',
        'icon' => null,
        'alias' => 'admin/banks',
        'tag' => 'banks',
        'parent_id' => $menus->where('tag', 'finances')->first()->id,
        'page_id' => $pages->where('alias', 'banks')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Зарплаты',
        'icon' => null,
        'alias' => 'admin/salaries',
        'tag' => 'salaries',
        'parent_id' => $menus->where('tag', 'finances')->first()->id,
        'page_id' => $pages->where('alias', 'salaries')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    // Производство
    [
        'name' => 'Помещения',
        'icon' => null,
        'alias' => 'admin/places',
        'tag' => 'places',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'places')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Поставщики',
        'icon' => null,
        'alias' => 'admin/suppliers',
        'tag' => 'suppliers',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'suppliers')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Рекламации',
        'icon' => null,
        'alias' => 'admin/claims',
        'tag' => 'claims',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'claims')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Заявки поставщикам',
        'icon' => null,
        'alias' => 'admin/applications',
        'tag' => 'applications',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'applications')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Товарные накладные',
        'icon' => null,
        'alias' => 'admin/consignments',
        'tag' => 'consignments',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'consignments')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    // Маркетинг
    [
        'name' => 'Сайты',
        'icon' => null,
        'alias' => 'admin/sites',
        'tag' => 'sites',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => $pages->where('alias', 'sites')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Альбомы',
        'icon' => null,
        'alias' => 'admin/albums',
        'tag' => 'albums',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => $pages->where('alias', 'albums')->first()->id,
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
        'alias' => 'admin/albums_categories',
        'tag' => 'albums_categories',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => $pages->where('alias', 'albums_categories')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Реклама',
        'icon' => null,
        'alias' => null,
        'tag' => 'advertisings',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => null,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Отзывы',
        'icon' => null,
        'alias' => 'admin/feedbacks',
        'tag' => 'feedbacks',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => $pages->where('alias', 'feedbacks')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    // Справочники
    [
        'name' => 'Компании',
        'icon' => null,
        'alias' => 'admin/companies',
        'tag' => 'companies',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'companies')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Списки',
        'icon' => null,
        'alias' => 'admin/booklists',
        'tag' => 'booklists',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'booklists')->first()->id,
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
        'alias' => 'admin/sectors',
        'tag' => 'sectors',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'sectors')->first()->id,
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
        'alias' => 'admin/cities',
        'tag' => 'cities',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'cities')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Производители',
        'icon' => null,
        'alias' => 'admin/manufacturers',
        'tag' => 'manufacturers',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'manufacturers')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Показатели',
        'icon' => null,
        'alias' => 'admin/indicators',
        'tag' => 'indicators',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'indicators')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],

    // Настройки
    [
        'name' => 'Роли',
        'icon' => null,
        'alias' => 'admin/roles',
        'tag' => 'roles',
        'parent_id' => $menus->where('tag', 'settings')->first()->id,
        'page_id' => $pages->where('alias', 'roles')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Этапы',
        'icon' => null,
        'alias' => 'admin/stages',
        'tag' => 'stages',
        'parent_id' => $menus->where('tag', 'settings')->first()->id,
        'page_id' => $pages->where('alias', 'stages')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Аккаунты',
        'icon' => null,
        'alias' => 'admin/accounts',
        'tag' => 'accounts',
        'parent_id' => $menus->where('tag', 'settings')->first()->id,
        'page_id' => $pages->where('alias', 'accounts')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],




    // -------------------------- Непонятные сущности, которые не знаю куда отнести --------------------------------------------

    // [
    //     'name' => 'Сущности',
    //     'icon' => null,
    //     'alias' => 'admin/entities',
    //     'tag' => 'entities',
    //     'parent_id' => 7,
    //     'page_id' => 6,
    //     'navigation_id' => 2,
    //     'company_id' => null,
    //     'system_item' => 1,
    //     'author_id' => 1,
    //     'display' => 1,
    //     'sort' => null,
    // ],

    // [
    //     'name' => 'Права',
    //     'icon' => null,
    //     'alias' => 'admin/rights',
    //     'tag' => 'rights',
    //     'parent_id' => 7,
    //     'page_id' => 8,
    //     'navigation_id' => 2,
    //     'company_id' => null,
    //     'system_item' => 1,
    //     'author_id' => 1,
    //     'display' => 1,
    //     'sort' => null,
    // ],

    // [
    //     'name' => 'Скрипты',
    //     'icon' => null,
    //     'alias' => 'admin/home',
    //     'tag' => 'scripts',
    //     'parent_id' => 28,
    //     'page_id' => 26,
    //     'navigation_id' => 2,
    //     'company_id' => null,
    //     'system_item' => 1,
    //     'author_id' => 1,
    //     'display' => 1,
    //     'sort' => 3,
    // ],

    // [
    //     'name' => 'Операционные расходы',
    //     'icon' => null,
    //     'alias' => 'admin/expenses',
    //     'tag' => 'expenses',
    //     'parent_id' => 43,
    //     'page_id' => 36,
    //     'navigation_id' => 2,
    //     'company_id' => null,
    //     'system_item' => 1,
    //     'author_id' => 1,
    //     'display' => 1,
    //     'sort' => 1,
    // ],

    // [
    //     'name' => 'Склад (Запасы продукции)',
    //     'icon' => null,
    //     'alias' => 'admin/stocks',
    //     'tag' => 'stocks',
    //     'parent_id' => 35,
    //     'page_id' => 50,
    //     'navigation_id' => 2,
    //     'company_id' => null,
    //     'system_item' => 1,
    //     'author_id' => 1,
    //     'display' => 1,
    //     'sort' => null,
    // ],

    // [
    //     'name' => 'Посты',
    //     'icon' => null,
    //     'alias' => 'admin/posts',
    //     'tag' => 'posts',
    //     'parent_id' => 48,
    //     'page_id' => 44,
    //     'navigation_id' => 2,
    //     'company_id' => null,
    //     'system_item' => 1,
    //     'author_id' => 1,
    //     'display' => 1,
    //     'sort' => null,
    // ],
]);

$menus = Menu::where('navigation_id', 2)->get(['id', 'tag']);

// Второй уровень вложенности
Menu::insert([
    [
        'name' => 'Рекламные кампании',
        'icon' => null,
        'alias' => 'admin/campaigns',
        'tag' => 'campaigns',
        'parent_id' => $menus->where('tag', 'advertisings')->first()->id,
        'page_id' => $pages->where('alias', 'campaigns')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
    [
        'name' => 'Социальные сети',
        'icon' => null,
        'alias' => null,
        'tag' => 'social_networks',
        'parent_id' => $menus->where('tag', 'advertisings')->first()->id,
        'page_id' => $pages->where('alias', 'social_networks')->first()->id,
        'navigation_id' => 2,
        'company_id' => null,
        'system_item' => 1,
        'author_id' => 1,
        'display' => 1,
        'sort' => null,
    ],
]);
}
}
