<?php

use Illuminate\Database\Seeder;

use App\Entity;
use App\Page;

class EntitiesTableSeeder extends Seeder
{

    public function run()
    {

        $pages = Page::where('site_id', 1)->get(['id', 'alias']);

        // Первый уровень
        Entity::insert([
            [
                'name' => 'Компании',
                'alias' => 'companies',
                'model' => 'Company',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'companies',
                'page_id' => $pages->firstWhere('alias', 'companies')->id,
            ],
            [
                'name' => 'Сущности',
                'alias' => 'entities',
                'model' => 'Entity',
                'rights' => false,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'entities',
                'page_id' => $pages->firstWhere('alias', 'entities')->id,
            ],
            [
                'name' => 'Роли',
                'alias' => 'roles',
                'model' => 'Role',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'roles',
                'page_id' => $pages->firstWhere('alias', 'roles')->id,
            ],


            [
                'name' => 'Сайты',
                'alias' => 'sites',
                'model' => 'Site',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'sites',
                'page_id' => $pages->firstWhere('alias', 'sites')->id,
            ],

            [
                'name' => 'Категории навигаци',
                'alias' => 'navigations_categories',
                'model' => 'NavigationsCategory',
                'rights' => false,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => null,
                'page_id' => null,
            ],

            [
                'name' => 'Категории альбомов',
                'alias' => 'albums_categories',
                'model' => 'AlbumsCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'albums_categories',
                'page_id' => $pages->firstWhere('alias', 'albums_categories')->id,
            ],
            [
                'name' => 'Должности',
                'alias' => 'positions',
                'model' => 'Position',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'positions',
                'page_id' => $pages->firstWhere('alias', 'positions')->id,
            ],
            [
                'name' => 'Списки',
                'alias' => 'booklists',
                'model' => 'Booklist',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'booklists',
                'page_id' => $pages->firstWhere('alias', 'booklists')->id,
            ],
            [
                'name' => 'Секторы',
                'alias' => 'sectors',
                'model' => 'Sector',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'sectors',
                'page_id' => $pages->firstWhere('alias', 'sectors')->id,
            ],
            [
                'name' => 'Категории товаров',
                'alias' => 'goods_categories',
                'model' => 'GoodsCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'products/articles_categories/goods_categories',
                'page_id' => $pages->firstWhere('alias', 'goods_categories')->id,
            ],
            [
                'name' => 'Категории сырья',
                'alias' => 'raws_categories',
                'model' => 'RawsCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'products/articles_categories/raws_categories',
                'page_id' => $pages->firstWhere('alias', 'raws_categories')->id,
            ],
            [
                'name' => 'Категории оборудования',
                'alias' => 'equipments_categories',
                'model' => 'EquipmentsCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'products/articles_categories/equipments_categories',
                'page_id' => $pages->firstWhere('alias', 'equipments_categories')->id,
            ],
            [
                'name' => 'Категории помещений',
                'alias' => 'rooms_categories',
                'model' => 'RoomsCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'products/articles_categories/rooms_categories',
                'page_id' => $pages->firstWhere('alias', 'rooms_categories')->id,
            ],
            [
                'name' => 'Категории расходных материалов',
                'alias' => 'expendables_categories',
                'model' => 'ExpendablesCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'products/articles_categories/expendables_categories',
                'page_id' => null,
            ],
            [
                'name' => 'Категории услуг',
                'alias' => 'services_categories',
                'model' => 'ServicesCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'products/processes_categories/services_categories',
                'page_id' => $pages->firstWhere('alias', 'services_categories')->id,
            ],
            [
                'name' => 'Категории рабочих процессов',
                'alias' => 'workflows_categories',
                'model' => 'WorkflowsCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'products/processes_categories/workflows_categories',
                'page_id' => $pages->firstWhere('alias', 'workflows_categories')->id,
            ],
            [
                'name' => 'Страны',
                'alias' => 'countries',
                'model' => 'Country',
                'rights' => false,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => null,
                'page_id' => null,
            ],

            [
                'name' => 'Населенные пункты',
                'alias' => 'cities',
                'model' => 'City',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'cities',
                'page_id' => $pages->firstWhere('alias', 'cities')->id,
            ],
            [
                'name' => 'Расписания',
                'alias' => 'schedules',
                'model' => 'Schedule',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'schedules',
                'page_id' => null,
            ],
            [
                'name' => 'Метрики',
                'alias' => 'metrics',
                'model' => 'Metric',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'metrics',
                'page_id' => null,
            ],
            [
                'name' => 'Лиды',
                'alias' => 'leads',
                'model' => 'Lead',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'leads',
                'page_id' => $pages->firstWhere('alias', 'leads')->id,
            ],
            [
                'name' => 'Каталоги товаров',
                'alias' => 'catalogs_goods',
                'model' => 'CatalogsGoods',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 1,
                'view_path' => 'catalogs_goods',
                'page_id' => $pages->firstWhere('alias', 'catalogs_goods')->id,
            ],
            [
                'name' => 'Каталоги услуг',
                'alias' => 'catalogs_services',
                'model' => 'CatalogsService',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 1,
                'view_path' => 'catalogs_services',
                'page_id' => $pages->firstWhere('alias', 'catalogs_services')->id,
            ],
            [
                'name' => 'Рубрикаторы',
                'alias' => 'rubricators',
                'model' => 'Rubricator',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 1,
                'view_path' => 'rubricators',
                'page_id' => $pages->firstWhere('alias', 'rubricators')->id,
            ],
            [
                'name' => 'Этапы',
                'alias' => 'stages',
                'model' => 'Stage',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'stages',
                'page_id' => $pages->firstWhere('alias', 'stages')->id,
            ],
            [
                'name' => 'Внутренние комментарии',
                'alias' => 'notes',
                'model' => 'Note',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'notes',
                'page_id' => null,
            ],
            [
                'name' => 'Задачи',
                'alias' => 'challenges',
                'model' => 'Challenge',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'challenges',
                'page_id' => $pages->firstWhere('alias', 'challenges')->id,
            ],
            [
                'name' => 'Рекламации',
                'alias' => 'claims',
                'model' => 'Claim',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'claims',
                'page_id' => $pages->firstWhere('alias', 'claims')->id,
            ],
            [
                'name' => 'Исходящие заказы',
                'alias' => 'orders',
                'model' => 'Order',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'orders',
                'page_id' => $pages->firstWhere('alias', 'orders')->id,
            ],
            [
                'name' => 'Отзывы',
                'alias' => 'feedbacks',
                'model' => 'Feedback',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'feedbacks',
                'page_id' => $pages->firstWhere('alias', 'feedbacks')->id,
            ],
            [
                'name' => 'Аккаунты',
                'alias' => 'accounts',
                'model' => 'Account',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'accounts',
                'page_id' => $pages->firstWhere('alias', 'accounts')->id,
            ],
            [
                'name' => 'Правила',
                'alias' => 'rules',
                'model' => 'Rule',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'rules',
                'page_id' => null,
            ],
            [
                'name' => 'Склады',
                'alias' => 'stocks',
                'model' => 'Stock',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'stocks',
                'page_id' => $pages->firstWhere('alias', 'stocks')->id,
            ],
            [
                'name' => 'Заявки поставщикам',
                'alias' => 'applications',
                'model' => 'Application',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'applications',
                'page_id' => $pages->firstWhere('alias', 'applications')->id,
            ],
            [
                'name' => 'Товарные накладные',
                'alias' => 'consignments',
                'model' => 'Consignment', 
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'consignments',
                'page_id' => $pages->firstWhere('alias', 'consignments')->id,
            ],
            [
                'name' => 'Показатели',
                'alias' => 'indicators',
                'model' => 'Indicator',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'indicators',
                'page_id' => $pages->firstWhere('alias', 'indicators')->id,
            ],
            [
                'name' => 'Настройка фоток',
                'alias' => 'photo_settings',
                'model' => 'PhotoSetting',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'photo_settings',
                'page_id' => null,
            ],
            [
                'name' => 'Сметы',
                'alias' => 'estimates',
                'model' => 'Estimate',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'estimates',
                'page_id' => $pages->firstWhere('alias', 'estimates')->id,
            ],
            [
                'name' => 'Группы артикулов',
                'alias' => 'articles_groups',
                'model' => 'ArticlesGroup',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'articles_groups',
                'page_id' => $pages->firstWhere('alias', 'articles_groups')->id,
            ],
            [
                'name' => 'Группы процессов',
                'alias' => 'processes_groups',
                'model' => 'ProcessesGroup',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'view_path' => 'processes_groups',
                'page_id' => $pages->firstWhere('alias', 'processes_groups')->id,
            ],


            // 04.06.19 - Чистка сущностей
            // [
            //     'name' => 'Правила',
            //     'alias' => 'rights',
            //     'model' => 'Right',
            //     'rights' => false,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'rights',
            //     'page_id' => $pages->firstWhere('alias', 'rights')->id,
            // ],
            // [
            //     'name' => 'Области',
            //     'alias' => 'regions',
            //     'model' => 'Region',
            //     'rights' => false,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'regions',
            //     'page_id' => null,
            // ],
            // [
            //     'name' => 'Районы',
            //     'alias' => 'areas',
            //     'model' => 'Area',
            //     'rights' => false,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'areas',
            //     'page_id' => null,
            // ],
            // [
            //     'name' => 'Категории правил',
            //     'alias' => 'category_right',
            //     'model' => 'Category_right',
            //     'rights' => false,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'category_right',
            //     'page_id' => null,
            // ],
            // [
            //     'name' => 'Категории единицы измерения',
            //     'alias' => 'units_categories',
            //     'model' => 'UnitsCategory',
            //     'rights' => false,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'units_categories',
            //     'page_id' => null,
            // ],
            // [
            //     'name' => 'Единицы измерения',
            //     'alias' => 'units',
            //     'model' => 'Unit',
            //     'rights' => false,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'ancestor_id' => Entity::whereAlias('units_categories')->first(['id'])->id,
            //     'view_path' => 'units',
            //     'page_id' => null,
            // ],
            // [
            //     'name' => 'Локации',
            //     'alias' => 'locations',
            //     'model' => 'Location',
            //     'rights' => false,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'locations',
            //     'page_id' => null,
            // ],
            // [
            //     'name' => 'Настройки',
            //     'alias' => 'settings',
            //     'model' => 'Setting',
            //     'rights' => false,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'settings',
            //     'page_id' => null,
            // ],
            // [
            //     'name' => 'Значения',
            //     'alias' => 'values',
            //     'model' => 'Value',
            //     'rights' => false,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'values',
            //     'page_id' => null,
            // ],
            // [
            //     'name' => 'Операционные расходы',
            //     'alias' => 'expenses',
            //     'model' => 'Expense',
            //     'rights' => true,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'expenses',
            //     'page_id' => $pages->firstWhere('alias', 'expenses')->id,
            // ],
            // [
            //     'name' => 'Зарплаты',
            //     'alias' => 'salaries',
            //     'model' => 'Salary',
            //     'rights' => true,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'salaries',
            //     'page_id' => $pages->firstWhere('alias', 'salaries')->id,
            // ],
            // [
            //     'name' => 'Рекламные кампании',
            //     'alias' => 'campaigns',
            //     'model' => 'Campaign',
            //     'rights' => true,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'campaigns',
            //     'page_id' => $pages->firstWhere('alias', 'campaigns')->id,
            // ],
            // [
            //     'name' => 'Социальные сети',
            //     'alias' => 'social_networks',
            //     'model' => 'SocialNetwork',
            //     'rights' => true,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'social_networks',
            //     'page_id' => $pages->firstWhere('alias', 'social_networks')->id,
            // ],
            // [
            //     'name' => 'Посты',
            //     'alias' => 'posts',
            //     'model' => 'Post',
            //     'rights' => true,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'posts',
            //     'page_id' => $pages->firstWhere('alias', 'posts')->id,
            // ],
            // [
            //     'name' => 'Поля',
            //     'alias' => 'fields',
            //     'model' => 'Field',
            //     'rights' => true,
            //     'system' => true,
            //     'author_id' => 1,
            //     'site' => 0,
            //     'view_path' => 'fields',
            //     'page_id' => $pages->firstWhere('alias', 'fields')->id,
            // ],
        ]);

        // Второй уровень
        Entity::insert([
            [
                'name' => 'Поставщики',
                'alias' => 'suppliers',
                'model' => 'Supplier',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
                'view_path' => 'suppliers',
                'page_id' => $pages->firstWhere('alias', 'suppliers')->id,
            ],
            [
                'name' => 'Клиенты',
                'alias' => 'clients',
                'model' => 'Client',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
                'view_path' => 'clients',
                'page_id' => $pages->firstWhere('alias', 'clients')->id,
            ],
            [
                'name' => 'Производители',
                'alias' => 'manufacturers',
                'model' => 'Manufacturer',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
                'view_path' => 'manufacturers',
                'page_id' => $pages->firstWhere('alias', 'manufacturers')->id,
            ],
            [
                'name' => 'Дилеры',
                'alias' => 'dealers',
                'model' => 'Dealer',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
                'view_path' => 'dealers',
                'page_id' => $pages->firstWhere('alias', 'dealers')->id,
            ],
            [
                'name' => 'Пользователи',
                'alias' => 'users',
                'model' => 'User',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
                'view_path' => 'users',
                'page_id' => $pages->firstWhere('alias', 'users')->id,
            ],
            [
                'name' => 'Отделы',
                'alias' => 'departments',
                'model' => 'Department',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
                'view_path' => 'departments',
                'page_id' => $pages->firstWhere('alias', 'departments')->id,
            ],
            [
                'name' => 'Штат',
                'alias' => 'staff',
                'model' => 'Staffer',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
                'view_path' => 'staff',
                'page_id' => $pages->firstWhere('alias', 'staff')->id,
            ],
            [
                'name' => 'Страницы',
                'alias' => 'pages',
                'model' => 'Page',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 1,
                'ancestor_id' => Entity::whereAlias('sites')->first(['id'])->id,
                'view_path' => 'pages',
                'page_id' => $pages->firstWhere('alias', 'pages')->id,
            ],
            [
                'name' => 'Навигации',
                'alias' => 'navigations',
                'model' => 'Navigation',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 1,
                'ancestor_id' => Entity::whereAlias('sites')->first(['id'])->id,
                'view_path' => 'navigations',
                'page_id' => $pages->firstWhere('alias', 'navigations')->id,
            ],

            [
                'name' => 'Альбомы',
                'alias' => 'albums',
                'model' => 'Album',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('albums_categories')->first(['id'])->id,
                'view_path' => 'albums',
                'page_id' => $pages->firstWhere('alias', 'albums')->id,
            ],
            [
                'name' => 'Товары',
                'alias' => 'goods',
                'model' => 'Goods',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('goods_categories')->first(['id'])->id,
                'view_path' => 'products/articles/goods',
                'page_id' => $pages->firstWhere('alias', 'goods')->id,
            ],
            [
                'name' => 'Сырьё',
                'alias' => 'raws',
                'model' => 'Raw',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('raws_categories')->first(['id'])->id,
                'view_path' => 'products/articles/raws',
                'page_id' => $pages->firstWhere('alias', 'raws')->id,
            ],
            [
                'name' => 'Оборудование',
                'alias' => 'equipments',
                'model' => 'Equipment',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('equipments_categories')->first(['id'])->id,
                'view_path' => 'products/articles/equipments',
                'page_id' => $pages->firstWhere('alias', 'equipments')->id,
            ],
            [
                'name' => 'Помещения',
                'alias' => 'rooms',
                'model' => 'Room',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('rooms_categories')->first(['id'])->id,
                'view_path' => 'products/articles/rooms',
                'page_id' => $pages->firstWhere('alias', 'rooms')->id,
            ],
            [
                'name' => 'Услуги',
                'alias' => 'services',
                'model' => 'Service',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('services_categories')->first(['id'])->id,
                'view_path' => 'products/processes/services',
                'page_id' => $pages->firstWhere('alias', 'services')->id,
            ],
            [
                'name' => 'Рабочие процессы',
                'alias' => 'workflows',
                'model' => 'Workflow',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('workflows_categories')->first(['id'])->id,
                'view_path' => 'products/processes/workflows',
                'page_id' => $pages->firstWhere('alias', 'workflows')->id,
            ],

            [
                'name' => 'Пункты каталогов товаров',
                'alias' => 'catalogs_goods_items',
                'model' => 'CatalogsGoodsItem',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('catalogs_goods')->first(['id'])->id,
                'view_path' => 'catalogs_goods_items',
                'page_id' => null,
            ],
            [
                'name' => 'Пункты каталогов услуг',
                'alias' => 'catalogs_services_items',
                'model' => 'CatalogsServicesItem',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('catalogs_services')->first(['id'])->id,
                'view_path' => 'catalogs_services_items',
                'page_id' => null,
            ],
            [
                'name' => 'Рубрики',
                'alias' => 'rubricators_items',
                'model' => 'RubricatorsItem',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('rubricators')->first(['id'])->id,
                'view_path' => 'rubricators_items',
                'page_id' => null,
            ],
            [
                'name' => 'Прайс услуги',
                'alias' => 'prices_services',
                'model' => 'PricesService',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('catalogs_services')->first(['id'])->id,
                'view_path' => 'prices_services',
                'page_id' => $pages->firstWhere('alias', 'prices_services')->id,
            ],
            [
                'name' => 'Прайс товара',
                'alias' => 'prices_goods',
                'model' => 'PricesGoods',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('catalogs_goods')->first(['id'])->id,
                'view_path' => 'prices_services',
                'page_id' => $pages->firstWhere('alias', 'prices_goods')->id,
            ],
            [
                'name' => 'Артикулы',
                'alias' => 'articles',
                'model' => 'Article',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('articles_groups')->first(['id'])->id,
                'view_path' => 'articles',
                'page_id' => null,
            ],
            [
                'name' => 'Процессы',
                'alias' => 'processes',
                'model' => 'Process',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('processes_groups')->first(['id'])->id,
                'view_path' => 'processes',
                'page_id' => null,
            ],
        ]);

        // Третий
        Entity::insert([
            [
                'name' => 'Меню',
                'alias' => 'menus',
                'model' => 'Menu',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('navigations')->first(['id'])->id,
                'view_path' => 'menus',
                'page_id' => $pages->firstWhere('alias', 'menus')->id,
            ],
            [
                'name' => 'Фотографии',
                'alias' => 'photos',
                'model' => 'Photo',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('albums')->first(['id'])->id,
                'view_path' => 'photos',
                'page_id' => $pages->firstWhere('alias', 'photos')->id,
            ],
            [
                'name' => 'Сотрудники',
                'alias' => 'employees',
                'model' => 'Employee',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('staff')->first(['id'])->id,
                'view_path' => 'employees',
                'page_id' => $pages->firstWhere('alias', 'employees')->id,
            ],
            [
                'name' => 'Новости',
                'alias' => 'news',
                'model' => 'News',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 1,
                'ancestor_id' => Entity::whereAlias('rubricators_items')->first(['id'])->id,
                'view_path' => 'news',
                'page_id' => $pages->firstWhere('alias', 'news')->id,
            ],
            [
                'name' => 'Банковские счета',
                'alias' => 'bank_accounts',
                'model' => 'BankAccount',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('rubricators_items')->first(['id'])->id,
                'view_path' => 'bank_accounts',
                'page_id' => null,
            ],
            [
                'name' => 'Склад товаров',
                'alias' => 'stock_goods',
                'model' => 'StockGoods',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('goods')->first(['id'])->id,
                'view_path' => 'stock_goods',
                'page_id' => $pages->firstWhere('alias', 'stock_goods')->id,
            ],
            [
                'name' => 'Склад сырья',
                'alias' => 'stock_raws',
                'model' => 'StockRaw',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('raws')->first(['id'])->id,
                'view_path' => 'stock_raws',
                'page_id' => $pages->firstWhere('alias', 'stock_raws')->id,
            ],

        ]);

        // Обновляем тмц
        // Entity::where('alias', 'raws')->update(['tmc' => 1]);
        // Entity::where('alias', 'goods')->update([
        //     'tmc' => 1,
        //     'consist_id' => Entity::where('alias', 'raws')->first(['id'])->id
        // ]);

}
}
