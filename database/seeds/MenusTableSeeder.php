<?php

use App\Observers\Traits\CategoriesTrait;
use Illuminate\Database\Seeder;
use App\Menu;
use App\Page;

class MenusTableSeeder extends Seeder
{

    use CategoriesTrait;

    public function run()
    {
        // Категории в сайдбаре
        Menu::insert([

            //  ЦУП
            // [
            //     'name' => 'ЦУП',
            //     'icon' => 'icon-mcc',
            //     'alias' => 'admin/dashboard',
            //     'tag' => 'mcc',
            //     'parent_id' => null,
            //     'page_id' => 31,
            //     'navigation_id' => 1,
            //     'company_id' => null,
            //     'system' => true,
            //     'author_id' => 1,
            //     'display' => true,
            //     'sort' => 1,
            // ],

            //  Процессы
            [
                'name' => 'Процессы',
                'icon' => 'icon-process',
                'alias' => null,
                'tag' => 'processes',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Персонал
            [
                'name' => 'Персонал',
                'icon' => 'icon-personal',
                'alias' => null,
                'tag' => 'personals',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Продажи
            [
                'name' => 'Продажи',
                'icon' => 'icon-sale',
                'alias' => null,
                'tag' => 'sales',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],


            //  Товары
            [
                'name' => 'Товары',
                'icon' => 'icon-goods',
                'alias' => null,
                'tag' => 'goods',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Сырье
            [
                'name' => 'Сырье',
                'icon' => 'icon-raw',
                'alias' => null,
                'tag' => 'raws',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Упаковка
            [
                'name' => 'Упаковка',
                'icon' => 'icon-container',
                'alias' => null,
                'tag' => 'containers',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Услуги
            [
                'name' => 'Услуги',
                'icon' => 'icon-service',
                'alias' => null,
                'tag' => 'services',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Финансы
            [
                'name' => 'Финансы',
                'icon' => 'icon-finance',
                'alias' => null,
                'tag' => 'finances',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Производство
            [
                'name' => 'Прoизводство',
                'icon' => 'icon-production',
                'alias' => null,
                'tag' => 'productions',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Маркетинг
            [
                'name' => 'Маркетинг',
                'icon' => 'icon-marketing',
                'alias' => null,
                'tag' => 'marketings',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Справочники
            [
                'name' => 'Справочники',
                'icon' => 'icon-guide',
                'alias' => null,
                'tag' => 'guides',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],

            //  Настройки
            [
                'name' => 'Настройки',
                'icon' => 'icon-setting',
                'alias' => null,
                'tag' => 'settings',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],
        ]);

$menus = Menu::where('navigation_id', 1)->get(['id', 'tag']);
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
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],

    // Персонал
    [
        'name' => 'Филиалы и отделы',
        'icon' => null,
        'alias' => 'admin/departments',
        'tag' => 'departments',
        'parent_id' => $menus->where('tag', 'personals')->first()->id,
        'page_id' => $pages->where('alias', 'departments')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Штат',
        'icon' => null,
        'alias' => 'admin/staff',
        'tag' => 'staff',
        'parent_id' => $menus->where('tag', 'personals')->first()->id,
        'page_id' => $pages->where('alias', 'staff')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 3,
    ],
    [
        'name' => 'Сотрудники',
        'icon' => null,
        'alias' => 'admin/employees',
        'tag' => 'employees',
        'parent_id' => $menus->where('tag', 'personals')->first()->id,
        'page_id' => $pages->where('alias', 'employees')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 2,
    ],
    [
        'name' => 'Должности',
        'icon' => null,
        'alias' => 'admin/positions',
        'tag' => 'positions',
        'parent_id' => $menus->where('tag', 'personals')->first()->id,
        'page_id' => $pages->where('alias', 'positions')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 4,
    ],

    // Продажи
    [
        'name' => 'Клиентские заказы',
        'icon' => null,
        'alias' => 'admin/estimates',
        'tag' => 'estimates',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'estimates')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 2,
    ],
    [
        'name' => 'Дилеры',
        'icon' => null,
        'alias' => 'admin/dealers',
        'tag' => 'dealers',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'dealers')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 4,
    ],
    [
        'name' => 'Лиды',
        'icon' => null,
        'alias' => 'admin/leads',
        'tag' => 'leads',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'leads')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Рекламации',
        'icon' => null,
        'alias' => 'admin/claims',
        'tag' => 'claims',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'claims')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 5,
    ],
    [
        'name' => 'Клиенты',
        'icon' => null,
        'alias' => 'admin/clients',
        'tag' => 'clients',
        'parent_id' => $menus->where('tag', 'sales')->first()->id,
        'page_id' => $pages->where('alias', 'clients')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 3,
    ],

    // Товары
    [
        'name' => 'Прайсы товаров',
        'icon' => null,
        'alias' => 'admin/catalogs_goods',
        'tag' => 'catalogs_goods',
        'parent_id' => $menus->where('tag', 'goods')->first()->id,
        'page_id' => $pages->where('alias', 'catalogs_goods')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 2,
    ],
    [
        'name' => 'Категории товаров',
        'icon' => null,
        'alias' => 'admin/goods_categories',
        'tag' => 'goods_categories',
        'parent_id' => $menus->where('tag', 'goods')->first()->id,
        'page_id' => $pages->where('alias', 'goods_categories')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 4,
    ],
    [
        'name' => 'Товары',
        'icon' => null,
        'alias' => 'admin/goods',
        'tag' => 'goods',
        'parent_id' => $menus->where('tag', 'goods')->first()->id,
        'page_id' => $pages->where('alias', 'goods')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Склады товаров',
        'icon' => null,
        'alias' => 'admin/goods_stocks',
        'tag' => 'goods_stocks',
        'parent_id' => $menus->where('tag', 'goods')->first()->id,
        'page_id' => $pages->where('alias', 'goods_stocks')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Группы артикулов',
        'icon' => null,
        'alias' => 'admin/articles_groups',
        'tag' => 'articles_groups',
        'parent_id' => $menus->where('tag', 'goods')->first()->id,
        'page_id' => $pages->where('alias', 'articles_groups')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 5,
    ],

    // Услуги
    [
        'name' => 'Прайсы услуг',
        'icon' => null,
        'alias' => 'admin/catalogs_services',
        'tag' => 'catalogs_services',
        'parent_id' => $menus->where('tag', 'services')->first()->id,
        'page_id' => $pages->where('alias', 'catalogs_services')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 2,
    ],
    [
        'name' => 'Категории услуг',
        'icon' => null,
        'alias' => 'admin/services_categories',
        'tag' => 'services_categories',
        'parent_id' => $menus->where('tag', 'services')->first()->id,
        'page_id' => $pages->where('alias', 'services_categories')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 3,
    ],
    [
        'name' => 'Услуги',
        'icon' => null,
        'alias' => 'admin/services',
        'tag' => 'services',
        'parent_id' => $menus->where('tag', 'services')->first()->id,
        'page_id' => $pages->where('alias', 'services')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Группы процессов',
        'icon' => null,
        'alias' => 'admin/processes_groups',
        'tag' => 'processes_groups',
        'parent_id' => $menus->where('tag', 'services')->first()->id,
        'page_id' => $pages->where('alias', 'processes_groups')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 4,
    ],


    // [
    //     'name' => 'Категории расходных материалов',
    //     'icon' => null,
    //     'alias' => 'admin/expendables_categories',
    //     'tag' => 'expendables_categories',
    //     'parent_id' => $menus->where('tag', 'products')->first()->id,
    //     'page_id' => $pages->where('alias', 'expendables_categories')->first()->id,
    //     'navigation_id' => 1,
    //     'company_id' => null,
    //     'system' => true,
    //     'author_id' => 1,
    //     'display' => true,
    //     'sort' => null,
    // ],


    // Финансы
    [
        'name' => 'Банки',
        'icon' => null,
        'alias' => 'admin/banks',
        'tag' => 'banks',
        'parent_id' => $menus->where('tag', 'finances')->first()->id,
        'page_id' => $pages->where('alias', 'banks')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Зарплаты',
        'icon' => null,
        'alias' => 'admin/salaries',
        'tag' => 'salaries',
        'parent_id' => $menus->where('tag', 'finances')->first()->id,
        'page_id' => $pages->where('alias', 'salaries')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],

    // Производство
    [
        'name' => 'Поставщики',
        'icon' => null,
        'alias' => 'admin/suppliers',
        'tag' => 'suppliers',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'suppliers')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Заявки поставщикам',
        'icon' => null,
        'alias' => 'admin/applications',
        'tag' => 'applications',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'applications')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Исходящие заказы',
        'icon' => null,
        'alias' => 'admin/orders',
        'tag' => 'orders',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'orders')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Товарные накладные',
        'icon' => null,
        'alias' => 'admin/consignments',
        'tag' => 'consignments',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'consignments')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Наряды на производство',
        'icon' => null,
        'alias' => 'admin/productions',
        'tag' => 'productions',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'productions')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Сырьё',
        'icon' => null,
        'alias' => 'admin/raws',
        'tag' => 'raws',
        'parent_id' => $menus->where('tag', 'raws')->first()->id,
        'page_id' => $pages->where('alias', 'raws')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Категории сырья',
        'icon' => null,
        'alias' => 'admin/raws_categories',
        'tag' => 'raws_categories',
        'parent_id' => $menus->where('tag', 'raws')->first()->id,
        'page_id' => $pages->where('alias', 'raws_categories')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 2,
    ],
    [
        'name' => 'Склады сырья',
        'icon' => null,
        'alias' => 'admin/raws_stocks',
        'tag' => 'raws_stocks',
        'parent_id' => $menus->where('tag', 'raws')->first()->id,
        'page_id' => $pages->where('alias', 'raws_stocks')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Упаковка',
        'icon' => null,
        'alias' => 'admin/containers',
        'tag' => 'containers',
        'parent_id' => $menus->where('tag', 'containers')->first()->id,
        'page_id' => $pages->where('alias', 'containers')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Категории упаковок',
        'icon' => null,
        'alias' => 'admin/containers_categories',
        'tag' => 'containers_categories',
        'parent_id' => $menus->where('tag', 'containers')->first()->id,
        'page_id' => $pages->where('alias', 'containers_categories')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 2,
    ],
    [
        'name' => 'Склады упаковок',
        'icon' => null,
        'alias' => 'admin/containers_stocks',
        'tag' => 'containers_stocks',
        'parent_id' => $menus->where('tag', 'containers')->first()->id,
        'page_id' => $pages->where('alias', 'containers_stocks')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Категории помещений',
        'icon' => null,
        'alias' => 'admin/rooms_categories',
        'tag' => 'rooms_categories',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'rooms_categories')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Помещения',
        'icon' => null,
        'alias' => 'admin/rooms',
        'tag' => 'rooms',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'rooms')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],

    [
        'name' => 'Склады',
        'icon' => null,
        'alias' => 'admin/stocks',
        'tag' => 'stocks',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'stocks')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Категории оборудования',
        'icon' => null,
        'alias' => 'admin/equipments_categories',
        'tag' => 'equipments_categories',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'equipments_categories')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Оборудование',
        'icon' => null,
        'alias' => 'admin/equipments',
        'tag' => 'equipments',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'equipments')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],

    [
        'name' => 'Рабочие процессы',
        'icon' => null,
        'alias' => 'admin/workflows',
        'tag' => 'workflows',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'workflows')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],

    [
        'name' => 'Категории рабочих процессов',
        'icon' => null,
        'alias' => 'admin/workflows_categories',
        'tag' => 'workflows_categories',
        'parent_id' => $menus->where('tag', 'productions')->first()->id,
        'page_id' => $pages->where('alias', 'workflows_categories')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
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
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 5,
    ],
    [
        'name' => 'Реклама',
        'icon' => null,
        'alias' => null,
        'tag' => 'advertisings',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => null,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Рубрикаторы новостей',
        'icon' => null,
        'alias' => 'admin/rubricators',
        'tag' => 'rubricators',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => $pages->where('alias', 'rubricators')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 2,
    ],
    [
        'name' => 'Новости',
        'icon' => null,
        'alias' => 'admin/news',
        'tag' => 'news',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => $pages->where('alias', 'news')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 1,
    ],
    [
        'name' => 'Отзывы',
        'icon' => null,
        'alias' => 'admin/feedbacks',
        'tag' => 'feedbacks',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => $pages->where('alias', 'feedbacks')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Категории альбомов',
        'icon' => null,
        'alias' => 'admin/albums_categories',
        'tag' => 'albums_categories',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => $pages->where('alias', 'albums_categories')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 4,
    ],
    [
        'name' => 'Альбомы',
        'icon' => null,
        'alias' => 'admin/albums',
        'tag' => 'albums',
        'parent_id' => $menus->where('tag', 'marketings')->first()->id,
        'page_id' => $pages->where('alias', 'albums')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => 3,
    ],


    // Справочники
    [
        'name' => 'Компании',
        'icon' => null,
        'alias' => 'admin/companies',
        'tag' => 'companies',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'companies')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Пользователи',
        'icon' => null,
        'alias' => 'admin/users',
        'tag' => 'users',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'users')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Списки',
        'icon' => null,
        'alias' => 'admin/booklists',
        'tag' => 'booklists',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'booklists')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Секторы',
        'icon' => null,
        'alias' => 'admin/sectors',
        'tag' => 'sectors',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'sectors')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Населенные пункты',
        'icon' => null,
        'alias' => 'admin/cities',
        'tag' => 'cities',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'cities')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Производители',
        'icon' => null,
        'alias' => 'admin/manufacturers',
        'tag' => 'manufacturers',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'manufacturers')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Показатели',
        'icon' => null,
        'alias' => 'admin/indicators',
        'tag' => 'indicators',
        'parent_id' => $menus->where('tag', 'guides')->first()->id,
        'page_id' => $pages->where('alias', 'indicators')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
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
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Этапы',
        'icon' => null,
        'alias' => 'admin/stages',
        'tag' => 'stages',
        'parent_id' => $menus->where('tag', 'settings')->first()->id,
        'page_id' => $pages->where('alias', 'stages')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Аккаунты',
        'icon' => null,
        'alias' => 'admin/accounts',
        'tag' => 'accounts',
        'parent_id' => $menus->where('tag', 'settings')->first()->id,
        'page_id' => $pages->where('alias', 'accounts')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
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
    //     'navigation_id' => 1,
    //     'company_id' => null,
    //     'system' => true,
    //     'author_id' => 1,
    //     'display' => true,
    //     'sort' => null,
    // ],

    // [
    //     'name' => 'Права',
    //     'icon' => null,
    //     'alias' => 'admin/rights',
    //     'tag' => 'rights',
    //     'parent_id' => 7,
    //     'page_id' => 8,
    //     'navigation_id' => 1,
    //     'company_id' => null,
    //     'system' => true,
    //     'author_id' => 1,
    //     'display' => true,
    //     'sort' => null,
    // ],

    // [
    //     'name' => 'Скрипты',
    //     'icon' => null,
    //     'alias' => 'admin/home',
    //     'tag' => 'scripts',
    //     'parent_id' => 28,
    //     'page_id' => 26,
    //     'navigation_id' => 1,
    //     'company_id' => null,
    //     'system' => true,
    //     'author_id' => 1,
    //     'display' => true,
    //     'sort' => 3,
    // ],

    // [
    //     'name' => 'Операционные расходы',
    //     'icon' => null,
    //     'alias' => 'admin/expenses',
    //     'tag' => 'expenses',
    //     'parent_id' => 43,
    //     'page_id' => 36,
    //     'navigation_id' => 1,
    //     'company_id' => null,
    //     'system' => true,
    //     'author_id' => 1,
    //     'display' => true,
    //     'sort' => 1,
    // ],



    // [
    //     'name' => 'Посты',
    //     'icon' => null,
    //     'alias' => 'admin/posts',
    //     'tag' => 'posts',
    //     'parent_id' => 48,
    //     'page_id' => 44,
    //     'navigation_id' => 1,
    //     'company_id' => null,
    //     'system' => true,
    //     'author_id' => 1,
    //     'display' => true,
    //     'sort' => null,
    // ],
]);

$menus = Menu::where('navigation_id', 1)->get(['id', 'tag']);

// Второй уровень вложенности
Menu::insert([
    [
        'name' => 'Рекламные кампании',
        'icon' => null,
        'alias' => 'admin/campaigns',
        'tag' => 'campaigns',
        'parent_id' => $menus->where('tag', 'advertisings')->first()->id,
        'page_id' => $pages->where('alias', 'campaigns')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
    [
        'name' => 'Социальные сети',
        'icon' => null,
        'alias' => null,
        'tag' => 'social_networks',
        'parent_id' => $menus->where('tag', 'advertisings')->first()->id,
        'page_id' => $pages->where('alias', 'social_networks')->first()->id,
        'navigation_id' => 1,
        'company_id' => null,
        'system' => true,
        'author_id' => 1,
        'display' => true,
        'sort' => null,
    ],
]);

        $menus = Menu::whereNull('parent_id')
            ->get();

        $this->recalculateCategories($menus);
}
}
