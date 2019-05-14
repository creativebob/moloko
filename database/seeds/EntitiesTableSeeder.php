<?php

use Illuminate\Database\Seeder;

use App\Entity;

class EntitiesTableSeeder extends Seeder
{

    public function run()
    {

        // Первый уровень
        Entity::insert([
            [
                'name' => 'Компании',
                'alias' => 'companies',
                'model' => 'Company',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Сущности',
                'alias' => 'entities',
                'model' => 'Entity',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Роли',
                'alias' => 'roles',
                'model' => 'Role',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Правила',
                'alias' => 'rights',
                'model' => 'Right',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Сайты',
                'alias' => 'sites',
                'model' => 'Site',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Категории навигаци',
                'alias' => 'navigations_categories',
                'model' => 'NavigationsCategory',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Новости',
                'alias' => 'news',
                'model' => 'News',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 1,
            ],
            [
                'name' => 'Категории альбомов',
                'alias' => 'albums_categories',
                'model' => 'AlbumsCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Должности',
                'alias' => 'positions',
                'model' => 'Position',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Списки',
                'alias' => 'booklists',
                'model' => 'Booklist',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Секторы',
                'alias' => 'sectors',
                'model' => 'Sector',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Папки',
                'alias' => 'folders',
                'model' => 'Folder',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Категории товаров',
                'alias' => 'goods_categories',
                'model' => 'GoodsCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Категории сырья',
                'alias' => 'raws_categories',
                'model' => 'RawsCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Категории помещений',
                'alias' => 'rooms_categories',
                'model' => 'RoomsCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Категории расходных материалов',
                'alias' => 'expendables_categories',
                'model' => 'ExpendablesCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Категории услуг',
                'alias' => 'services_categories',
                'model' => 'ServicesCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Категории рабочих процессов',
                'alias' => 'workflows_categories',
                'model' => 'WorkflowsCategory',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Страны',
                'alias' => 'countries',
                'model' => 'Country',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Области',
                'alias' => 'regions',
                'model' => 'Region',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Районы',
                'alias' => 'areas',
                'model' => 'Area',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Населенные пункты',
                'alias' => 'cities',
                'model' => 'City',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Сущности связанные с городами',
                'alias' => 'city_entity',
                'model' => 'CityEntity',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Категории правил',
                'alias' => 'category_right',
                'model' => 'Category_right',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Сущности с прикрепленными альбомами',
                'alias' => 'album_entity',
                'model' => 'AlbumEntity',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Категории единицы измерения',
                'alias' => 'units_categories',
                'model' => 'UnitsCategory',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Расписания',
                'alias' => 'schedules',
                'model' => 'Schedule',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Локации',
                'alias' => 'locations',
                'model' => 'Location',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Настройки',
                'alias' => 'settings',
                'model' => 'Setting',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Метрики',
                'alias' => 'metrics',
                'model' => 'Metric',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Состав',
                'alias' => 'compositions',
                'model' => 'Composition',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Значения',
                'alias' => 'values',
                'model' => 'Value',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Операционные расходы',
                'alias' => 'expenses',
                'model' => 'Expense',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Зарплаты',
                'alias' => 'salaries',
                'model' => 'Salary',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Рекламные кампании',
                'alias' => 'campaigns',
                'model' => 'Campaign',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,

            ],
            [
                'name' => 'Социальные сети',
                'alias' => 'social_networks',
                'model' => 'SocialNetwork',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,

            ],
            [
                'name' => 'Лиды',
                'alias' => 'leads',
                'model' => 'Lead',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Каталоги',
                'alias' => 'catalogs',
                'model' => 'Catalog',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 1,
            ],
            [
                'name' => 'Этапы',
                'alias' => 'stages',
                'model' => 'Stage',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Внутренние комментарии',
                'alias' => 'notes',
                'model' => 'Note',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Задачи',
                'alias' => 'challenges',
                'model' => 'Challenge',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Рекламации',
                'alias' => 'claims',
                'model' => 'Claim',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Исходящие заказы',
                'alias' => 'orders',
                'model' => 'Order',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Отзывы',
                'alias' => 'feedbacks',
                'model' => 'Feedback',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Посты',
                'alias' => 'posts',
                'model' => 'Post',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Аккаунты',
                'alias' => 'accounts',
                'model' => 'Account',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Поля',
                'alias' => 'fields',
                'model' => 'Field',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Правила',
                'alias' => 'rules',
                'model' => 'Rule',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Склад (Запасы продукции)',
                'alias' => 'stocks',
                'model' => 'Stock',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Заявки поставщикам',
                'alias' => 'applications',
                'model' => 'Application',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Товарные накладные',
                'alias' => 'consignments',
                'model' => 'Сonsignment',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Показатели',
                'alias' => 'indicators',
                'model' => 'Indicator',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Настройка фоток',
                'alias' => 'photo_settings',
                'model' => 'PhotoSetting',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Клиентские заказы',
                'alias' => 'estimates',
                'model' => 'Estimate',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Группы артикулов',
                'alias' => 'articles_groups',
                'model' => 'ArticlesGroup',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
            [
                'name' => 'Группы процессов',
                'alias' => 'processes_groups',
                'model' => 'ProcessesGroup',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
            ],
        ]);

        // Второй уровень
        Entity::insert([

            [
                'name' => 'Поставщики',
                'alias' => 'suppliers',
                'model' => 'Supplier',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
            ],
            [
                'name' => 'Клиенты',
                'alias' => 'clients',
                'model' => 'Client',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
            ],
            [
                'name' => 'Производители',
                'alias' => 'manufacturers',
                'model' => 'Manufacturer',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
            ],
            [
                'name' => 'Дилеры',
                'alias' => 'dealers',
                'model' => 'Dealer',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
            ],
            [
                'name' => 'Пользователи',
                'alias' => 'users',
                'model' => 'User',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
            ],
            [
                'name' => 'Отделы',
                'alias' => 'departments',
                'model' => 'Department',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
            ],
            [
                'name' => 'Штат',
                'alias' => 'staff',
                'model' => 'Staffer',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
            ],
            [
                'name' => 'Сотрудники',
                'alias' => 'employees',
                'model' => 'Employee',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
            ],
            [
                'name' => 'Страницы',
                'alias' => 'pages',
                'model' => 'Page',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 1,
                'ancestor_id' => Entity::whereAlias('sites')->first(['id'])->id,
            ],
            [
                'name' => 'Навигации',
                'alias' => 'navigations',
                'model' => 'Navigation',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 1,
                'ancestor_id' => Entity::whereAlias('sites')->first(['id'])->id,
            ],

            [
                'name' => 'Альбомы',
                'alias' => 'albums',
                'model' => 'Album',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('albums_categories')->first(['id'])->id,
            ],
            [
                'name' => 'Товары',
                'alias' => 'goods',
                'model' => 'Goods',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('goods_categories')->first(['id'])->id,
            ],
            [
                'name' => 'Сырьё',
                'alias' => 'raws',
                'model' => 'Raw',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('raws_categories')->first(['id'])->id,
            ],
            [
                'name' => 'Помещения',
                'alias' => 'rooms',
                'model' => 'Room',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('rooms_categories')->first(['id'])->id,
            ],
            [
                'name' => 'Услуги',
                'alias' => 'services',
                'model' => 'Service',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('services_categories')->first(['id'])->id,
            ],
            [
                'name' => 'Рабочие процессы',
                'alias' => 'workflows',
                'model' => 'Workflow',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('workflows_categories')->first(['id'])->id,
            ],
            [
                'name' => 'Единицы измерения',
                'alias' => 'units',
                'model' => 'Unit',
                'rights_minus' => 1,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('units_categories')->first(['id'])->id,
            ],
            [
                'name' => 'Состав каталогов',
                'alias' => 'catalogs_items',
                'model' => 'CatalogsItem',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('catalogs')->first(['id'])->id,
            ],
            [
                'name' => 'Артикулы',
                'alias' => 'articles',
                'model' => 'Article',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('articles_groups')->first(['id'])->id,
            ],
            [
                'name' => 'Процессы',
                'alias' => 'processes',
                'model' => 'Process',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('processes_groups')->first(['id'])->id,
            ],
        ]);

        // Третий
        Entity::insert([
            [
                'name' => 'Меню',
                'alias' => 'menus',
                'model' => 'Menu',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('navigations')->first(['id'])->id,
            ],
            [
                'name' => 'Фотографии',
                'alias' => 'photos',
                'model' => 'Photo',
                'rights_minus' => null,
                'system_item' => 1,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('albums')->first(['id'])->id,
            ],
        ]);

        // Обновляем тмц
        Entity::where('alias', 'raws')->update(['tmc' => 1]);
        Entity::where('alias', 'goods')->update([
            'tmc' => 1,
            'consist_id' => Entity::where('alias', 'raws')->first(['id'])->id
        ]);

        // [
        //     'name' => 'Режимы товаров',
        //     'alias' => 'goods_modes',
        //     'model' => 'GoodsMode',
        //     'rights_minus' => null,
        //     'system_item' => 1,
        //     'author_id' => 1,
        //     'site' => 0,
        // ],
        // [
        //     'name' => 'Режимы услуг',
        //     'alias' => 'services_modes',
        //     'model' => 'ServicesMode',
        //     'rights_minus' => 1,
        //     'system_item' => 1,
        //     'author_id' => 1,
        //     'site' => 0,
        // ],
        // [
        //     'name' => 'Режимы сырья',
        //     'alias' => 'raws_modes',
        //     'model' => 'RawsMode',
        //     'rights_minus' => 1,
        //     'system_item' => 1,
        //     'author_id' => 1,
        //     'site' => 0,
        // ],
        // [
        //     'name' => 'Артикулы услуг',
        //     'alias' => 'services_articles',
        //     'model' => 'ServicesArticle',
        //     'rights_minus' => null,
        //     'system_item' => 1,
        //     'author_id' => 1,
        //     'site' => 0,
        // ],

}
}
