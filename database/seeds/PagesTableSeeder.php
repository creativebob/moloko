<?php

use Illuminate\Database\Seeder;

use App\Page;

class PagesTableSeeder extends Seeder
{

    public function run()
    {
        // Создаем страницы для crm системы
        Page::insert([

            // ------------------ Сущности и права ---------------------------
            [
                'name' => 'Сущности',
                'site_id' => 1,
                'title' => 'Сущности',
                'description' => 'Сущности системы',
                'alias' => 'entities',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Роли',
                'site_id' => 1,
                'title' => 'Группы доступа (роли)',
                'description' => 'Пользовательские группы доступа',
                'alias' => 'roles',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Правила доступа',
                'site_id' => 1,
                'title' => 'Правила доступа зерегистрированные для системы',
                'description' => 'Правила доступа',
                'alias' => 'rights',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],


            // ----------------- Основной блок (База) ------------------------

            // Компания
            [
                'name' => 'Населенные пункты',
                'site_id' => 1,
                'title' => 'Населенные пункты',
                'description' => 'Области, районы и города',
                'alias' => 'cities',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Секторы',
                'site_id' => 1,
                'title' => 'Секторы',
                'description' => 'Секторы',
                'alias' => 'sectors',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Этапы',
                'site_id' => 1,
                'title' => 'Этапы',
                'description' => 'Этапы',
                'alias' => 'stages',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Компании',
                'site_id' => 1,
                'title' => 'Компании',
                'description' => 'Компании в системе автоматизации',
                'alias' => 'companies',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Пользователи системы',
                'site_id' => 1,
                'title' => 'Пользователи системы',
                'description' => 'Пользователи системы',
                'alias' => 'users',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Филиалы и отделы',
                'site_id' => 1,
                'title' => 'Филиалы и отделы',
                'description' => 'Филиалы и отделы',
                'alias' => 'departments',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Должности',
                'site_id' => 1,
                'title' => 'Должности',
                'description' => 'Должности компании',
                'alias' => 'positions',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Штат',
                'site_id' => 1,
                'title' => 'Штат компании',
                'description' => 'Штат компании',
                'alias' => 'staff',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Сотрудники',
                'site_id' => 1,
                'title' => 'Сотрудники компании',
                'description' => 'Сотрудники компании',
                'alias' => 'employees',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // Контрагенты
            [
                'name' => 'Поставщики',
                'site_id' => 1,
                'title' => 'Поставщики',
                'description' => 'Поставщики товаров и услуг',
                'alias' => 'suppliers',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Клиенты',
                'site_id' => 1,
                'title' => 'Клиенты',
                'description' => 'Все наши клиенты: от физического лица до юридических организаций.',
                'alias' => 'clients',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Производители',
                'site_id' => 1,
                'title' => 'Производители',
                'description' => 'Производители чего-либо - вот кто рулит миром!',
                'alias' => 'manufacturers',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Дилеры',
                'site_id' => 1,
                'title' => 'Дилеры',
                'description' => 'Дилеры',
                'alias' => 'dealers',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Банки',
                'site_id' => 1,
                'title' => 'Банки',
                'description' => 'Банки',
                'alias' => 'banks',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // Инструменты
            [
                'name' => 'Списки',
                'site_id' => 1,
                'title' => 'Списки',
                'description' => 'Списки любых сущностей',
                'alias' => 'booklists',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Рабочий стол',
                'site_id' => 1,
                'title' => 'Рабочий стол',
                'description' => 'Рабочий стол',
                'alias' => 'dashboard',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],




            // ------------------------- Маркетинг --------------------------------
            [
                'name' => 'Сайты',
                'site_id' => 1,
                'title' => 'Сайты компании',
                'description' => 'Сайты компаний в системе, и сама система',
                'alias' => 'sites',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Страницы сайта',
                'site_id' => 1,
                'title' => 'Страницы сайта',
                'description' => 'Страницы определенного сайта',
                'alias' => 'pages',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Навигации',
                'site_id' => 1,
                'title' => 'Навигации сайта',
                'description' => 'Навигации',
                'alias' => 'navigations',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Меню',
                'site_id' => 1,
                'title' => 'Меню',
                'description' => 'Меню',
                'alias' => 'menus',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Аккаунты',
                'site_id' => 1,
                'title' => 'Аккаунты',
                'description' => 'Аккаунты',
                'alias' => 'accounts',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Социальные сети',
                'site_id' => 1,
                'title' => 'Социальные сети',
                'description' => 'Социальные сети',
                'alias' => 'social_networks',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Отзывы',
                'site_id' => 1,
                'title' => 'Отзывы',
                'description' => 'Отзывы',
                'alias' => 'feedbacks',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Рекламные кампании',
                'site_id' => 1,
                'title' => 'Рекламные кампании',
                'description' => 'Рекламные кампании',
                'alias' => 'campaigns',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // Альбомы
            [
                'name' => 'Категории альбомов',
                'site_id' => 1,
                'title' => 'Категории альбомов',
                'description' => 'Категории альбомов фотографий компании',
                'alias' => 'albums_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Альбомы',
                'site_id' => 1,
                'title' => 'Альбомы',
                'description' => 'Альбомы фотографий компании',
                'alias' => 'albums',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Фотографии альбома',
                'site_id' => 1,
                'title' => 'Фотографии альбома',
                'description' => 'Фотографии',
                'alias' => 'photos',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // ------------------------------ Продукция --------------------------

            [
                'name' => 'Группы артикулов',
                'site_id' => 1,
                'title' => 'Группы артикулов',
                'description' => 'Группы артикулов',
                'alias' => 'articles_groups',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            [
                'name' => 'Группы процессов',
                'site_id' => 1,
                'title' => 'Группы процессов',
                'description' => 'Группы процессов',
                'alias' => 'processes_groups',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // Товары
            [
                'name' => 'Категории товаров',
                'site_id' => 1,
                'title' => 'Категории товаров',
                'description' => 'Категории товаров',
                'alias' => 'goods_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Товары',
                'site_id' => 1,
                'title' => 'Товары',
                'description' => 'Товары',
                'alias' => 'goods',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // Сырье
            [
                'name' => 'Категории сырья',
                'site_id' => 1,
                'title' => 'Категории сырья',
                'description' => 'Категории сырья',
                'alias' => 'raws_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Сырьё',
                'site_id' => 1,
                'title' => 'Сырьё',
                'description' => 'Сырьё',
                'alias' => 'raws',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // Помещения
            [
                'name' => 'Категории помещений',
                'site_id' => 1,
                'title' => 'Категории помещений',
                'description' => 'Категории помещений',
                'alias' => 'rooms_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Помещения',
                'site_id' => 1,
                'title' => 'Помещения',
                'description' => 'Помещения',
                'alias' => 'rooms',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // Услуги
            [
                'name' => 'Категории услуг',
                'site_id' => 1,
                'title' => 'Категории услуг',
                'description' => 'Категории услуг',
                'alias' => 'services_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Услуги',
                'site_id' => 1,
                'title' => 'Услуги',
                'description' => 'Услуги',
                'alias' => 'services',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            // Рабочие процессы
            [
                'name' => 'Категории рабочих процессов',
                'site_id' => 1,
                'title' => 'Категории рабочих процессов',
                'description' => 'Категории рабочих процессов',
                'alias' => 'workflows_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Рабочие процессы',
                'site_id' => 1,
                'title' => 'Рабочие процессы',
                'description' => 'Рабочие процессы',
                'alias' => 'workflows',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],



            // Расходные материалы
            // [
            //     'name' => 'Категории расходных материалов',
            //     'site_id' => 1,
            //     'title' => 'Категории расходных материалов',
            //     'description' => 'Категории расходных материалов',
            //     'alias' => 'expendables_categories',
            //     'company_id' => null,
            //     'system_item' => 1,
            //     'author_id' => 1,
            //     'display' => 1,
            // ],



            // ----------------- Нераспределенные ---------------------------
            [
                'name' => 'Операционные расходы',
                'site_id' => 1,
                'title' => 'Операционные расходы',
                'description' => 'Операционные расходы',
                'alias' => 'expenses',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Зарплаты',
                'site_id' => 1,
                'title' => 'Зарплаты',
                'description' => 'Зарплаты',
                'alias' => 'salaries',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Лиды',
                'site_id' => 1,
                'title' => 'Лиды',
                'description' => 'Обращения в компанию',
                'alias' => 'leads',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Каталоги',
                'site_id' => 1,
                'title' => 'Каталоги',
                'description' => 'Каталоги',
                'alias' => 'catalogs',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Пункты каталога',
                'site_id' => 1,
                'title' => 'Пункты каталога',
                'description' => 'Пункты каталога',
                'alias' => 'catalogs_items',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],

            [
                'name' => 'Продукция на сайте',
                'site_id' => 1,
                'title' => 'Продукция на сайте',
                'description' => 'Продукция на сайте',
                'alias' => 'catalog_products',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Посты',
                'site_id' => 1,
                'title' => 'Посты',
                'description' => 'Посты',
                'alias' => 'posts',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Заказы',
                'site_id' => 1,
                'title' => 'Заказы',
                'description' => 'Заказы',
                'alias' => 'orders',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Рекламации',
                'site_id' => 1,
                'title' => 'Рекламации',
                'description' => 'Рекламации',
                'alias' => 'claims',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Задачи',
                'site_id' => 1,
                'title' => 'Задачи',
                'description' => 'Задачи',
                'alias' => 'challenges',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Склад',
                'site_id' => 1,
                'title' => 'Склад',
                'description' => 'Склад',
                'alias' => 'stocks',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Заказы поставщикам',
                'site_id' => 1,
                'title' => 'Заказы поставщикам',
                'description' => 'Заказы поставщикам',
                'alias' => 'applications',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Товарные накладные',
                'site_id' => 1,
                'title' => 'Товарные накладные',
                'description' => 'Товарные накладные',
                'alias' => 'consignments',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Показатели',
                'site_id' => 1,
                'title' => 'Показатели',
                'description' => 'Показатели',
                'alias' => 'indicators',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Расчеты',
                'site_id' => 1,
                'title' => 'Расчеты',
                'description' => 'Расчеты',
                'alias' => 'estimates',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
            [
                'name' => 'Новости',
                'site_id' => 1,
                'title' => 'Новости',
                'description' => 'Новости',
                'alias' => 'news',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => 1,
            ],
        ]);
}
}
