<?php

use Illuminate\Database\Seeder;

class EntitiesTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('entities')->insert([
            // 1 ЦУП
            [
                'name' => 'Компании',
                'alias' => 'companies',
                'model' => 'Company',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Пользователи',
                'alias' => 'users',
                'model' => 'User',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Отделы',
                'alias' => 'departments',
                'model' => 'Department',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Штат',
                'alias' => 'staff',
                'model' => 'Staffer',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Сотрудники',
                'alias' => 'employees',
                'model' => 'Employee',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 6 Настройка
            [
                'name' => 'Сущности',
                'alias' => 'entities',
                'model' => 'Entity',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Роли',
                'alias' => 'roles',
                'model' => 'Role',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Правила',
                'alias' => 'rights',
                'model' => 'Right',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 9 Маркетинг
            [
                'name' => 'Сайты',
                'alias' => 'sites',
                'model' => 'Site',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Страницы',
                'alias' => 'pages',
                'model' => 'Page',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Новости',
                'alias' => 'news',
                'model' => 'News',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Альбомы',
                'alias' => 'albums',
                'model' => 'Album',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 13 Списки
            [
                'name' => 'Должности',
                'alias' => 'positions',
                'model' => 'Position',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Списки',
                'alias' => 'booklists',
                'model' => 'Booklist',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Секторы',
                'alias' => 'sectors',
                'model' => 'Sector',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Папки',
                'alias' => 'folders',
                'model' => 'Folder',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],

            [
                'name' => 'Категории альбомов',
                'alias' => 'albums_categories',
                'model' => 'AlbumsCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Фотографии',
                'alias' => 'photos',
                'model' => 'Photo',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 19 Продукция
            [
                'name' => 'Категории товаров',
                'alias' => 'goods_categories',
                'model' => 'GoodsCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Категории услуг',
                'alias' => 'services_categories',
                'model' => 'ServicesCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Категории сырья',
                'alias' => 'raws_categories',
                'model' => 'RawsCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Товары',
                'alias' => 'goods',
                'model' => 'Goods',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Услуги',
                'alias' => 'services',
                'model' => 'Service',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Сырьё',
                'alias' => 'raws',
                'model' => 'Raw',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Помещения',
                'alias' => 'places',
                'model' => 'Place',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 27 Сущности, связаныне с одной страницей
            [
                'name' => 'Навигации',
                'alias' => 'navigations',
                'model' => 'Navigation',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            [
                'name' => 'Меню',
                'alias' => 'menus',
                'model' => 'Menu',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            [
                'name' => 'Области',
                'alias' => 'regions',
                'model' => 'Region',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Районы',
                'alias' => 'areas',
                'model' => 'Area',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Населенные пункты',
                'alias' => 'cities',
                'model' => 'City',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // Сущности без страниц отображения

            // Настройка
            [
                'name' => 'Категории правил',
                'alias' => 'category_right',
                'model' => 'Category_right',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Категории навигаци',
                'alias' => 'navigations_categories',
                'model' => 'NavigationsCategory',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],

            [
                'name' => 'Сущности с прикрепленными альбомами',
                'alias' => 'album_entity',
                'model' => 'AlbumEntity',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Единицы измерения',
                'alias' => 'units',
                'model' => 'Unit',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Страны',
                'alias' => 'countries',
                'model' => 'Country',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Сущности связанные с городами',
                'alias' => 'city_entity',
                'model' => 'CityEntity',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Расписания',
                'alias' => 'schedules',
                'model' => 'Schedule',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Локации',
                'alias' => 'locations',
                'model' => 'Location',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Настройки',
                'alias' => 'settings',
                'model' => 'Setting',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Метрики',
                'alias' => 'metrics',
                'model' => 'Metric',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Состав',
                'alias' => 'compositions',
                'model' => 'Composition',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Значения',
                'alias' => 'values',
                'model' => 'Value',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Типы помещений',
                'alias' => 'places_types',
                'model' => 'PlacesType',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Режимы товаров',
                'alias' => 'goods_modes',
                'model' => 'GoodsMode',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Режимы услуг',
                'alias' => 'services_modes',
                'model' => 'ServicesMode',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Режимы сырья',
                'alias' => 'raws_modes',
                'model' => 'RawsMode',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Группы товаров',
                'alias' => 'goods_products',
                'model' => 'GoodsProduct',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Группы услуг',
                'alias' => 'services_products',
                'model' => 'ServicesProduct',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Группы сырья',
                'alias' => 'raws_products',
                'model' => 'RawsProduct',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Поставщики',
                'alias' => 'suppliers',
                'model' => 'Supplier',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Клиенты',
                'alias' => 'clients',
                'model' => 'Client',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Производители',
                'alias' => 'manufacturers',
                'model' => 'Manufacturer',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Операционные расходы',
                'alias' => 'expenses',
                'model' => 'Expense',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Зарплаты',
                'alias' => 'salaries',
                'model' => 'Salary',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Рекламные кампании',
                'alias' => 'campaigns',
                'model' => 'Campaign',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Социальные сети',
                'alias' => 'social_networks',
                'model' => 'SocialNetwork',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],
            [
                'name' => 'Лиды',
                'alias' => 'leads',
                'model' => 'Lead',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            [
                'name' => 'Артикулы услуг',
                'alias' => 'services_articles',
                'model' => 'ServicesArticle',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            [
                'name' => 'Каталоги',
                'alias' => 'catalogs',
                'model' => 'Catalog',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            [
                'name' => 'Каталоги с продукцией',
                'alias' => 'catalog_products',
                'model' => 'CatalogProduct',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            [
                'name' => 'Этапы',
                'alias' => 'stages',
                'model' => 'Stage',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            [
                'name' => 'Внутренние комментарии',
                'alias' => 'notes',
                'model' => 'Note',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 65
            [
                'name' => 'Задачи',
                'alias' => 'challenges',
                'model' => 'Challenge',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],


            // 66
            [
                'name' => 'Рекламации',
                'alias' => 'claims',
                'model' => 'Claim',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 67
            [
                'name' => 'Заказы',
                'alias' => 'orders',
                'model' => 'Order',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 68
            [
                'name' => 'Отзывы',
                'alias' => 'feedbacks',
                'model' => 'Feedback',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 69
            [
                'name' => 'Посты',
                'alias' => 'posts',
                'model' => 'Post',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 70
            [
                'name' => 'Аккаунты',
                'alias' => 'accounts',
                'model' => 'Account',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 71
            [
                'name' => 'Поля',
                'alias' => 'fields',
                'model' => 'Field',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 72
            [
                'name' => 'Правила',
                'alias' => 'rules',
                'model' => 'Rule',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 73
            [
                'name' => 'Склад (Запасы продукции)',
                'alias' => 'stocks',
                'model' => 'Stock',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 74
            [
                'name' => 'Категории расходных материалов',
                'alias' => 'expendables_categories',
                'model' => 'ExpendablesCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 75
            [
                'name' => 'Заказы поставщикам',
                'alias' => 'applications',
                'model' => 'Application',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 76
            [
                'name' => 'Товарные накладные',
                'alias' => 'consignments',
                'model' => 'Сonsignment',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 77
            [
                'name' => 'Показатели',
                'alias' => 'indicators',
                'model' => 'Indicator',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
            ],

            // 78
            [
                'name' => 'Настройка фоток',
                'alias' => 'photo_settings',
                'model' => 'PhotoSetting',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
            ],

        ]);

}
}
