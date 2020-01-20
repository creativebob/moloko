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
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Роли',
                'site_id' => 1,
                'title' => 'Группы доступа (роли)',
                'description' => 'Пользовательские группы доступа',
                'alias' => 'roles',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Правила доступа',
                'site_id' => 1,
                'title' => 'Правила доступа зерегистрированные для системы',
                'description' => 'Правила доступа',
                'alias' => 'rights',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
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
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Секторы',
                'site_id' => 1,
                'title' => 'Секторы',
                'description' => 'Секторы',
                'alias' => 'sectors',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Этапы',
                'site_id' => 1,
                'title' => 'Этапы',
                'description' => 'Этапы',
                'alias' => 'stages',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Компании',
                'site_id' => 1,
                'title' => 'Компании',
                'description' => 'Компании в системе автоматизации',
                'alias' => 'companies',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Пользователи системы',
                'site_id' => 1,
                'title' => 'Пользователи системы',
                'description' => 'Пользователи системы',
                'alias' => 'users',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Филиалы и отделы',
                'site_id' => 1,
                'title' => 'Филиалы и отделы',
                'description' => 'Филиалы и отделы',
                'alias' => 'departments',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Должности',
                'site_id' => 1,
                'title' => 'Должности',
                'description' => 'Должности компании',
                'alias' => 'positions',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Штат',
                'site_id' => 1,
                'title' => 'Штат компании',
                'description' => 'Штат компании',
                'alias' => 'staff',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Сотрудники',
                'site_id' => 1,
                'title' => 'Сотрудники компании',
                'description' => 'Сотрудники компании',
                'alias' => 'employees',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Контрагенты
            [
                'name' => 'Поставщики',
                'site_id' => 1,
                'title' => 'Поставщики',
                'description' => 'Поставщики товаров и услуг',
                'alias' => 'suppliers',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Клиенты',
                'site_id' => 1,
                'title' => 'Клиенты',
                'description' => 'Все наши клиенты: от физического лица до юридических организаций.',
                'alias' => 'clients',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Производители',
                'site_id' => 1,
                'title' => 'Производители',
                'description' => 'Производители чего-либо - вот кто рулит миром!',
                'alias' => 'manufacturers',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Дилеры',
                'site_id' => 1,
                'title' => 'Дилеры',
                'description' => 'Дилеры',
                'alias' => 'dealers',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Банки',
                'site_id' => 1,
                'title' => 'Банки',
                'description' => 'Банки',
                'alias' => 'banks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Инструменты
            [
                'name' => 'Списки',
                'site_id' => 1,
                'title' => 'Списки',
                'description' => 'Списки любых сущностей',
                'alias' => 'booklists',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Рабочий стол',
                'site_id' => 1,
                'title' => 'Рабочий стол',
                'description' => 'Рабочий стол',
                'alias' => 'dashboard',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],




            // ------------------------- Маркетинг --------------------------------
            [
                'name' => 'Домены',
                'site_id' => 1,
                'title' => 'Домены',
                'description' => 'Домены',
                'alias' => 'domains',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Сайты',
                'site_id' => 1,
                'title' => 'Сайты компании',
                'description' => 'Сайты компаний в системе, и сама система',
                'alias' => 'sites',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Страницы сайта',
                'site_id' => 1,
                'title' => 'Страницы сайта',
                'description' => 'Страницы определенного сайта',
                'alias' => 'pages',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Навигации',
                'site_id' => 1,
                'title' => 'Навигации сайта',
                'description' => 'Навигации',
                'alias' => 'navigations',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Меню',
                'site_id' => 1,
                'title' => 'Меню',
                'description' => 'Меню',
                'alias' => 'menus',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Аккаунты',
                'site_id' => 1,
                'title' => 'Аккаунты',
                'description' => 'Аккаунты',
                'alias' => 'accounts',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Источники трафика',
                'site_id' => 1,
                'title' => 'Источники трафика',
                'description' => 'Источники трафика',
                'alias' => 'sources',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],            
            [
                'name' => 'Социальные сети',
                'site_id' => 1,
                'title' => 'Социальные сети',
                'description' => 'Социальные сети',
                'alias' => 'social_networks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Отзывы',
                'site_id' => 1,
                'title' => 'Отзывы',
                'description' => 'Отзывы',
                'alias' => 'feedbacks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Рекламные кампании',
                'site_id' => 1,
                'title' => 'Рекламные кампании',
                'description' => 'Рекламные кампании',
                'alias' => 'campaigns',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Альбомы
            [
                'name' => 'Категории альбомов',
                'site_id' => 1,
                'title' => 'Категории альбомов',
                'description' => 'Категории альбомов фотографий компании',
                'alias' => 'albums_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Альбомы',
                'site_id' => 1,
                'title' => 'Альбомы',
                'description' => 'Альбомы фотографий компании',
                'alias' => 'albums',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Фотографии альбома',
                'site_id' => 1,
                'title' => 'Фотографии альбома',
                'description' => 'Фотографии',
                'alias' => 'photos',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // ------------------------------ Продукция --------------------------

            [
                'name' => 'Группы артикулов',
                'site_id' => 1,
                'title' => 'Группы артикулов',
                'description' => 'Группы артикулов',
                'alias' => 'articles_groups',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            [
                'name' => 'Группы процессов',
                'site_id' => 1,
                'title' => 'Группы процессов',
                'description' => 'Группы процессов',
                'alias' => 'processes_groups',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Товары
            [
                'name' => 'Категории товаров',
                'site_id' => 1,
                'title' => 'Категории товаров',
                'description' => 'Категории товаров',
                'alias' => 'goods_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Товары',
                'site_id' => 1,
                'title' => 'Товары',
                'description' => 'Товары',
                'alias' => 'goods',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Склады товаров',
                'site_id' => 1,
                'title' => 'Склады товаров',
                'description' => 'Склады товаров',
                'alias' => 'goods_stocks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Сырье
            [
                'name' => 'Категории сырья',
                'site_id' => 1,
                'title' => 'Категории сырья',
                'description' => 'Категории сырья',
                'alias' => 'raws_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Сырьё',
                'site_id' => 1,
                'title' => 'Сырьё',
                'description' => 'Сырьё',
                'alias' => 'raws',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Склады сырья',
                'site_id' => 1,
                'title' => 'Склады сырья',
                'description' => 'Склады сырья',
                'alias' => 'raws_stocks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Упаковка
            [
                'name' => 'Категории упаковок',
                'site_id' => 1,
                'title' => 'Категории упаковок',
                'description' => 'Категории упаковок',
                'alias' => 'containers_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Упаковки',
                'site_id' => 1,
                'title' => 'Упаковки',
                'description' => 'Упаковки',
                'alias' => 'containers',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
	        [
		        'name' => 'Склады упаковок',
		        'site_id' => 1,
		        'title' => 'Склады упаковок',
		        'description' => 'Склады упаковок',
		        'alias' => 'containers_stocks',
		        'company_id' => null,
		        'system' => true,
		        'author_id' => 1,
		        'display' => true,
	        ],

            // Вложения
            [
                'name' => 'Категории вложений',
                'site_id' => 1,
                'title' => 'Категории вложений',
                'description' => 'Категории вложений',
                'alias' => 'attachments_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Вложения',
                'site_id' => 1,
                'title' => 'Вложения',
                'description' => 'Вложения',
                'alias' => 'attachments',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Склады вложений',
                'site_id' => 1,
                'title' => 'Склады вложений',
                'description' => 'Склады вложений',
                'alias' => 'attachments_stocks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Инструменты
            [
                'name' => 'Категории инструментов',
                'site_id' => 1,
                'title' => 'Категории инструментов',
                'description' => 'Категории инструментов',
                'alias' => 'tools_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Инструменты',
                'site_id' => 1,
                'title' => 'Инструменты',
                'description' => 'Инструменты',
                'alias' => 'tools',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Склады инструментов',
                'site_id' => 1,
                'title' => 'Склады инструментов',
                'description' => 'Склады инструментов',
                'alias' => 'tools_stocks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Помещения
            [
                'name' => 'Категории помещений',
                'site_id' => 1,
                'title' => 'Категории помещений',
                'description' => 'Категории помещений',
                'alias' => 'rooms_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Помещения',
                'site_id' => 1,
                'title' => 'Помещения',
                'description' => 'Помещения',
                'alias' => 'rooms',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Услуги
            [
                'name' => 'Категории услуг',
                'site_id' => 1,
                'title' => 'Категории услуг',
                'description' => 'Категории услуг',
                'alias' => 'services_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Услуги',
                'site_id' => 1,
                'title' => 'Услуги',
                'description' => 'Услуги',
                'alias' => 'services',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // Рабочие процессы
            [
                'name' => 'Категории рабочих процессов',
                'site_id' => 1,
                'title' => 'Категории рабочих процессов',
                'description' => 'Категории рабочих процессов',
                'alias' => 'workflows_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Рабочие процессы',
                'site_id' => 1,
                'title' => 'Рабочие процессы',
                'description' => 'Рабочие процессы',
                'alias' => 'workflows',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],



            // Расходные материалы
            // [
            //     'name' => 'Категории расходных материалов',
            //     'site_id' => 1,
            //     'title' => 'Категории расходных материалов',
            //     'description' => 'Категории расходных материалов',
            //     'alias' => 'expendables_categories',
            //     'company_id' => null,
            //     'system' => true,
            //     'author_id' => 1,
            //     'display' => true,
            // ],



            // ----------------- Нераспределенные ---------------------------
            [
                'name' => 'Операционные расходы',
                'site_id' => 1,
                'title' => 'Операционные расходы',
                'description' => 'Операционные расходы',
                'alias' => 'expenses',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Зарплаты',
                'site_id' => 1,
                'title' => 'Зарплаты',
                'description' => 'Зарплаты',
                'alias' => 'salaries',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Лиды',
                'site_id' => 1,
                'title' => 'Лиды',
                'description' => 'Обращения в компанию',
                'alias' => 'leads',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Прайсы товаров',
                'site_id' => 1,
                'title' => 'Прайсы товаров',
                'description' => 'Прайсы товаров',
                'alias' => 'catalogs_goods',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Пункты прайса товаров',
                'site_id' => 1,
                'title' => 'Пункты прайса товаров',
                'description' => 'Пункты прайса товаров',
                'alias' => 'catalogs_goods_items',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Прайсы товаров',
                'site_id' => 1,
                'title' => 'Прайсы товаров',
                'description' => 'Прайсы товаров',
                'alias' => 'prices_goods',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Прайсы услуг',
                'site_id' => 1,
                'title' => 'Прайсы услуг',
                'description' => 'Прайсы услуг',
                'alias' => 'catalogs_services',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Пункты прайса услуг',
                'site_id' => 1,
                'title' => 'Пункты прайса услуг',
                'description' => 'Пункты прайса услуг',
                'alias' => 'catalogs_services_items',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Прайсы услуг',
                'site_id' => 1,
                'title' => 'Прайсы услуг',
                'description' => 'Прайсы услуг',
                'alias' => 'prices_services',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],


            [
                'name' => 'Рубрикатор',
                'site_id' => 1,
                'title' => 'Рубрикатор',
                'description' => 'Рубрикатор',
                'alias' => 'rubricators',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Рубрика',
                'site_id' => 1,
                'title' => 'Рубрика',
                'description' => 'Рубрика',
                'alias' => 'rubricators_items',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Новости',
                'site_id' => 1,
                'title' => 'Новости',
                'description' => 'Новости',
                'alias' => 'news',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            // [
            //     'name' => 'Продукция на сайте',
            //     'site_id' => 1,
            //     'title' => 'Продукция на сайте',
            //     'description' => 'Продукция на сайте',
            //     'alias' => 'catalog_products',
            //     'company_id' => null,
            //     'system' => true,
            //     'author_id' => 1,
            //     'display' => true,
            // ],
            [
                'name' => 'Посты',
                'site_id' => 1,
                'title' => 'Посты',
                'description' => 'Посты',
                'alias' => 'posts',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Заказы',
                'site_id' => 1,
                'title' => 'Заказы',
                'description' => 'Заказы',
                'alias' => 'orders',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Рекламации',
                'site_id' => 1,
                'title' => 'Рекламации',
                'description' => 'Рекламации',
                'alias' => 'claims',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Задачи',
                'site_id' => 1,
                'title' => 'Задачи',
                'description' => 'Задачи',
                'alias' => 'challenges',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Склад',
                'site_id' => 1,
                'title' => 'Склад',
                'description' => 'Склад',
                'alias' => 'stocks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Заказы поставщикам',
                'site_id' => 1,
                'title' => 'Заказы поставщикам',
                'description' => 'Заказы поставщикам',
                'alias' => 'applications',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            [
                'name' => 'Товарные накладные',
                'site_id' => 1,
                'title' => 'Товарные накладные',
                'description' => 'Товарные накладные',
                'alias' => 'consignments',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Наряды на производство',
                'site_id' => 1,
                'title' => 'Наряды на производство',
                'description' => 'Наряды на производство',
                'alias' => 'productions',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            [
                'name' => 'Показатели',
                'site_id' => 1,
                'title' => 'Показатели',
                'description' => 'Показатели',
                'alias' => 'indicators',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Сметы',
                'site_id' => 1,
                'title' => 'Сметы',
                'description' => 'Сметы',
                'alias' => 'estimates',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            [
                'name' => 'Продвижения',
                'site_id' => 1,
                'title' => 'Продвижения',
                'description' => 'Продвижения',
                'alias' => 'promotions',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

            [
                'name' => 'Рассылки',
                'site_id' => 1,
                'title' => 'Рассылки',
                'description' => 'Рассылки',
                'alias' => 'dispatches',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],

        ]);
}
}
