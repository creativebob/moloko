<?php

use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{

    public function run()
    {
        // Создаем страницы для crm системы
        DB::table('pages')->insert([
            // 1 ЦУП
            [
                'name' => 'Компании',
                'site_id' => 1,
                'title' => 'Компании',
                'description' => 'Компании в системе автоматизации',
                'alias' => 'companies',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
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
                'display' => null,
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
                'display' => null,
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
                'display' => null,
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
                'display' => null,
            ],

            // 6 Настройка
            [
                'name' => 'Сущности',
                'site_id' => 1,
                'title' => 'Сущности',
                'description' => 'Сущности системы',
                'alias' => 'entities',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
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
                'display' => null,
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
                'display' => null,
            ],

            // 9 Маркетинг
            [
                'name' => 'Сайты',
                'site_id' => 1,
                'title' => 'Сайты компании',
                'description' => 'Сайты компаний в системе, и сама система',
                'alias' => 'sites',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
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
                'display' => null,
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

            // 13 Списки
            [
                'name' => 'Должности',
                'site_id' => 1,
                'title' => 'Должности',
                'description' => 'Должности компании',
                'alias' => 'positions',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],
            [
                'name' => 'Списки',
                'site_id' => 1,
                'title' => 'Списки',
                'description' => 'Списки любых сущностей',
                'alias' => 'booklists',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
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
                'display' => null,
            ],
            [
                'name' => 'Папки',
                'site_id' => 1,
                'title' => 'Папки',
                'description' => 'Папки (директории)',
                'alias' => 'folders',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],
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

            // 19 Продукция
            [
                'name' => 'Категории товаров',
                'site_id' => 1,
                'title' => 'Категории товаров',
                'description' => 'Категории товаров',
                'alias' => 'goods_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],
            [
                'name' => 'Категории услуг',
                'site_id' => 1,
                'title' => 'Категории услуг',
                'description' => 'Категории услуг',
                'alias' => 'services_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],
            [
                'name' => 'Категории сырья',
                'site_id' => 1,
                'title' => 'Категории сырья',
                'description' => 'Категории сырья',
                'alias' => 'raws_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
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
                'display' => null,
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
                'display' => null,
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
                'display' => null,
            ],
            [
                'name' => 'Помещения',
                'site_id' => 1,
                'title' => 'Помещения',
                'description' => 'Помещения',
                'alias' => 'places',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            [
                'name' => 'Продажи',
                'site_id' => 1,
                'title' => 'Страница должности',
                'description' => 'Должность в компании',
                'alias' => 'home',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 27 Страницы, связанные со многими сущностями
            [
                'name' => 'Навигации',
                'site_id' => 1,
                'title' => 'Навигации сайта',
                'description' => 'Навигации',
                'alias' => 'navigations',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],
            [
                'name' => 'Населенные пункты',
                'site_id' => 1,
                'title' => 'Населенные пункты',
                'description' => 'Области, районы и города',
                'alias' => 'cities',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],
            [
                'name' => 'Поставщики',
                'site_id' => 1,
                'title' => 'Поставщики',
                'description' => 'Поставщики товаров и услуг',
                'alias' => 'suppliers',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
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
                'display' => null,
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
                'display' => null,
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
                'display' => null,
            ],

            [
                'name' => 'Группы товаров',
                'site_id' => 1,
                'title' => 'Группы товаров',
                'description' => 'Группы товаров',
                'alias' => 'goods_products',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            [
                'name' => 'Группы услуг',
                'site_id' => 1,
                'title' => 'Группы услуг',
                'description' => 'Группы услуг',
                'alias' => 'services_products',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 35
            [
                'name' => 'Группы сырья',
                'site_id' => 1,
                'title' => 'Группы сырья',
                'description' => 'Группы сырья',
                'alias' => 'raws_products',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 36
            [
                'name' => 'Операционные расходы',
                'site_id' => 1,
                'title' => 'Операционные расходы',
                'description' => 'Операционные расходы',
                'alias' => 'expenses',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 37
            [
                'name' => 'Зарплаты',
                'site_id' => 1,
                'title' => 'Зарплаты',
                'description' => 'Зарплаты',
                'alias' => 'salaries',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 38
            [
                'name' => 'Лиды',
                'site_id' => 1,
                'title' => 'Лиды',
                'description' => 'Обращения в компанию',
                'alias' => 'leads',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 39
            [
                'name' => 'Рекламные кампании',
                'site_id' => 1,
                'title' => 'Рекламные кампании',
                'description' => 'Рекламные кампании',
                'alias' => 'campaigns',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 40
            [
                'name' => 'Социальные сети',
                'site_id' => 1,
                'title' => 'Социальные сети',
                'description' => 'Социальные сети',
                'alias' => 'social_networks',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 41
            [
                'name' => 'Каталог продукции для сайта',
                'site_id' => 1,
                'title' => 'Каталог продукции для сайта',
                'description' => 'Каталог продукции для сайта',
                'alias' => 'catalogs',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 42
            [
                'name' => 'Продукция на сайте',
                'site_id' => 1,
                'title' => 'Продукция на сайте',
                'description' => 'Продукция на сайте',
                'alias' => 'catalog_products',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 43
            [
                'name' => 'Этапы',
                'site_id' => 1,
                'title' => 'Этапы',
                'description' => 'Этапы',
                'alias' => 'stages',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 44
            [
                'name' => 'Посты',
                'site_id' => 1,
                'title' => 'Посты',
                'description' => 'Посты',
                'alias' => 'posts',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 45
            [
                'name' => 'Аккаунты',
                'site_id' => 1,
                'title' => 'Аккаунты',
                'description' => 'Аккаунты',
                'alias' => 'accounts',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 46
            [
                'name' => 'Отзывы',
                'site_id' => 1,
                'title' => 'Отзывы',
                'description' => 'Отзывы',
                'alias' => 'feedback',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 47
            [
                'name' => 'Заказы',
                'site_id' => 1,
                'title' => 'Заказы',
                'description' => 'Заказы',
                'alias' => 'orders',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 48
            [
                'name' => 'Рекламации',
                'site_id' => 1,
                'title' => 'Рекламации',
                'description' => 'Рекламации',
                'alias' => 'claims',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 49
            [
                'name' => 'Задачи',
                'site_id' => 1,
                'title' => 'Задачи',
                'description' => 'Задачи',
                'alias' => 'challenges',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 50
            [
                'name' => 'Склад (Запасы продукции)',
                'site_id' => 1,
                'title' => 'Склад (Запасы продукции)',
                'description' => 'Склад (Запасы продукции)',
                'alias' => 'stocks',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 51
            [
                'name' => 'Категории расходных материалов',
                'site_id' => 1,
                'title' => 'Категории расходных материалов',
                'description' => 'Категории расходных материалов',
                'alias' => 'expendables_categories',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 52
            [
                'name' => 'Заказы поставщикам',
                'site_id' => 1,
                'title' => 'Заказы поставщикам',
                'description' => 'Заказы поставщикам',
                'alias' => 'applications',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 53
            [
                'name' => 'Товарные накладные',
                'site_id' => 1,
                'title' => 'Товарные накладные',
                'description' => 'Товарные накладные',
                'alias' => 'consignments',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
            ],

            // 54
            [
                'name' => 'Показатели',
                'site_id' => 1,
                'title' => 'Показатели',
                'description' => 'Показатели',
                'alias' => 'indicators',
                'company_id' => null,
                'system_item' => 1,
                'author_id' => 1,
                'display' => null,
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
                'display' => null,
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
                'display' => null,
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
                'display' => null,
            ],

            // [
            //     'name' => 'Категории продукции',
            //     'site_id' => 1,
            //     'title' => 'Категории продукции',
            //     'description' => 'Категории продукции компании',
            //     'alias' => 'products_categories',
            //     'company_id' => null,
            //     'system_item' => 1,
            //     'author_id' => 1,
            //     'display' => 1,
            // ],

            // [
            //     'name' => 'Продукция',
            //     'site_id' => 1,
            //     'title' => 'Продукция',
            //     'description' => 'Продукция компании',
            //     'alias' => 'products',
            //     'company_id' => null,
            //     'system_item' => 1,
            //     'author_id' => 1,
            //     'display' => 1,
            // ],

            // [
            //     'name' => 'Типы помещений',
            //     'site_id' => 1,
            //     'title' => 'Типы помещений',
            //     'description' => 'Типы помещений',
            //     'alias' => 'places_types',
            //     'company_id' => null,
            //     'system_item' => 1,
            //     'author_id' => 1,
            //     'display' => null,
            // ],
        ]);
}
}
