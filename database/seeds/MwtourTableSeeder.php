<?php

use App\Article;
use App\ArticlesGroup;
use App\Room;
use App\RoomsCategory;
use App\Stock;
use Illuminate\Database\Seeder;

use App\Company;
use App\Location;
use App\Department;
use App\User;
use App\Staffer;
use App\Employee;
use App\Manufacturer;
use App\Supplier;
use App\Vendor;
use App\Domain;
use App\Site;
use App\Page;
use App\Navigation;
use App\Menu;
use App\CatalogsService;
use App\CatalogsServicesItem;
use App\CatalogsGoods;
use App\CatalogsGoodsItem;
use App\ToolsCategory;
use App\ServicesCategory;

class MwtourTableSeeder extends Seeder
{

    public function run()
    {

        Location::insert([
        	[
        		'address' => 'квартал 120, дом 16',
        		'city_id' => 2,
        		'country_id' => 1,
        		'author_id' => 1,
        		'parse_count' => 0,
        		'answer_count' => 1
        	]
        ]);


        // Компания
        Company::insert([
        	[
        		'name' => 'Magic World Tour',
        		'alias' => 'mw-tour',
        		'location_id' => 1,
        		'legal_form_id' => 2,
        		'sector_id' => 14,
        		'author_id' => 1
        	]
        ]);

        $company = Company::first();
        $company->phones()->attach(1, ['main' => 1]);

        Department::insert([
        	[
        		'name' => 'Иркутский филиал',
        		'company_id' => 1,
        		'location_id' => 1,
                'email' => 'shatalin.1997@mail.ru',
                'display' => true,
        		'author_id' => 3
        	]
        ]);

        $department = Department::first();
        $department->phones()->attach(1, ['main' => 1]);

        User::insert([
            [
                'login' => 'director',
                'email' => 'shatalin.1997@mail.ru',
                'password' => bcrypt('123123'),
                'nickname' => 'director',
                'first_name' => 'Александр',
                'second_name' => 'Шаталин',
                'location_id' => 1,
                'user_type' => 1,
                'access_block' => 0,
                'filial_id' => 1,
                'company_id' => 1,
                'gender' => 1,
            ],
        ]);

        User::where('god', 1)->update(['company_id' => 1]);

        $user = User::where('login', 'director')->first();
        $user->phones()->attach(1, ['main' => 1]);

        Staffer::insert([
            [
                'company_id' => 1,
                'position_id' => 1,
                'department_id' => 1,
                'filial_id' => 1,
                'author_id' => 1,
                'user_id' => User::where('login', 'director')->first(['id'])->id,
            ],
        ]);

        Employee::insert([
            [
                'staffer_id' => Staffer::where('position_id', 1)->value('id'),
                'user_id' => User::where('login', 'director')->value('id'),
                'employment_date' => today(),
                'company_id' => 1,
                'author_id' => 1,
            ],
        ]);

        DB::table('role_user')->insert([
            [
                'role_id' => 1,
                'department_id' => 1,
                'position_id' => 1,
                'user_id' => User::where('login', 'director')->value('id'),
            ],
        ]);

        Site::insert([
        	[
		        'name' => 'Magic World Tour',
                'alias' => 'mwtour',
		        'company_id' => 1,
                'author_id' => 4,
                'api_token' => str_random(60),
        	],
        ]);

        $site = Site::where('alias', 'mwtour')->first();

        Domain::insert([
            'domain' => 'mw-tour.local',
            'company_id' => 1,
            'author_id' => 4,
            'site_id' => $site->id
        ]);

        $domain = Domain::where('domain', 'mw-tour.local')->first();
        $domain->filials()->attach(1);

        Page::insert([
            [
                'name' => 'Туры',
                'site_id' => 2,
                'title' => 'Туры',
                'subtitle' => null,
                'description' => 'Туры',
                'alias' => 'tours',
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'content' => 'Страница со списком туров'
            ],
            [
                'name' => 'Команда',
                'site_id' => 2,
                'title' => 'Команда',
                'subtitle' => '',
                'description' => 'Команда',
                'alias' => 'team',
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'content' => null
            ],
            [
                'name' => 'Личный кабинет',
                'site_id' => 2,
                'title' => 'Личный кабинет',
                'subtitle' => null,
                'description' => 'Личный кабинет',
                'alias' => 'profile',
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'content' => null
            ],
        ]);

        Navigation::insert([
        	[
                'name' => 'Главная',
                'site_id' => 2,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'align_id' => 1
            ],
        ]);

        Menu::insert([
        	[
                'name' => 'Туры',
                'navigation_id' => 2,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'tag' => 'tours',
                'page_id' => Page::where(['site_id' => 2, 'alias' => 'tours'])->value('id'),
            ],
            [
                'name' => 'Команда',
                'navigation_id' => 2,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'tag' => 'team',
                'page_id' => Page::where(['site_id' => 2, 'alias' => 'team'])->value('id'),
            ],
            [
                'name' => 'Личный кабинет',
                'navigation_id' => 2,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'tag' => 'profile',
                'page_id' => Page::where(['site_id' => 2, 'alias' => 'profile'])->value('id'),
            ],

        ]);

        CatalogsService::insert([
            [
                'name' => 'Наши туры',
                'description' => 'Наши туры',
                'slug' => \Str::slug('Наши туры'),
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        $catalog_services = CatalogsService::first();
        $catalog_services->filials()->attach(1);

        // 1 уровень
        CatalogsServicesItem::insert([
            [
                'catalogs_service_id' => 1,
                'name' => 'Байкал',
                'slug' => \Str::slug('Байкал'),
                'level' => 1,
                'title' => 'Байкал',
                'company_id' => 1,
                'display' => true,
                'author_id' => 4
            ],
            [
                'catalogs_service_id' => 1,
                'name' => 'Алтай',
                'slug' => \Str::slug('Алтай'),
                'level' => 1,
                'title' => 'Алтай',
                'company_id' => 1,
                'display' => true,
                'author_id' => 4
            ],
            [
                'catalogs_service_id' => 1,
                'name' => 'Монголия',
                'slug' => \Str::slug('Монголия'),
                'level' => 1,
                'title' => 'Монголия',
                'company_id' => 1,
                'display' => true,
                'author_id' => 4
            ]
        ]);

        // // 2 уровень
        // $parent = CatalogsServicesItem::where('name', 'Диагностика и ремонт')
        //     ->first([
        //         'id',
        //         'slug',
        //     ]);

        // CatalogsServicesItem::insert([
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'ТНВД и инжекторы Common Rail',
        //         'slug' => $parent->slug . '/' . \Str::slug('ТНВД и инжекторы Common Rail'),
        //         'level' => 2,
        //         'title' => 'ТНВД и инжекторы Common Rail',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->id,
        //     ],
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Насос форсунки и насосные секции',
        //         'slug' => $parent->slug . '/' . \Str::slug('Насос форсунки и насосные секции'),
        //         'level' => 2,
        //         'title' => 'Насос форсунки и насосные секции',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->id,
        //     ],
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Механические ТНВД рядного типа',
        //         'slug' => $parent->slug . '/' . \Str::slug('Механические ТНВД рядного типа'),
        //         'level' => 2,
        //         'title' => 'Механические ТНВД рядного типа',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->id,
        //     ],
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'ТНВД типа V4',
        //         'slug' => $parent->slug . '/' . \Str::slug('ТНВД типа V4'),
        //         'level' => 2,
        //         'title' => 'ТНВД типа V4',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->id,
        //     ],
        // ]);

        // $parent = CatalogsServicesItem::where('name', 'Прочие')
        //     ->first([
        //         'id',
        //         'slug',
        //     ]);

        // CatalogsServicesItem::insert([
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Прошивка процессора',
        //         'slug' => $parent->slug . '/' . \Str::slug('Прошивка процессора'),
        //         'level' => 2,
        //         'title' => 'Прошивка процессора',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->id,
        //     ],
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Удаление сажевого фильтра',
        //         'slug' => $parent->slug . '/' . \Str::slug('Удаление сажевого фильтра'),
        //         'level' => 2,
        //         'title' => 'Удаление сажевого фильтра',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->id,
        //     ],
        // ]);

        // // 3 уровень
        // $parent = CatalogsServicesItem::where('name', 'ТНВД и инжекторы Common Rail')
        //     ->first([
        //         'id',
        //         'slug',
        //         'category_id'
        //     ]);

        // CatalogsServicesItem::insert([
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Bosch',
        //         'slug' => $parent->slug . '/' . \Str::slug('Bosch'),
        //         'level' => 3,
        //         'title' => 'Bosch',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->category_id,
        //     ],
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Denso',
        //         'slug' => $parent->slug . '/' . \Str::slug('Denso'),
        //         'level' => 3,
        //         'title' => 'Denso',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->category_id,
        //     ],
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Delphi',
        //         'slug' => $parent->slug . '/' . \Str::slug('Delphi'),
        //         'level' => 3,
        //         'title' => 'Delphi',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->category_id,
        //     ],
        // ]);

        // $parent = CatalogsServicesItem::where('name', 'Насос форсунки и насосные секции')
        //     ->first([
        //         'id',
        //         'slug',
        //         'category_id'
        //     ]);

        // CatalogsServicesItem::insert([
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Bosch',
        //         'slug' => $parent->slug . '/' . \Str::slug('Bosch'),
        //         'level' => 3,
        //         'title' => 'Bosch',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->category_id,
        //     ],
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Delphi',
        //         'slug' => $parent->slug . '/' . \Str::slug('Delphi'),
        //         'level' => 3,
        //         'title' => 'Delphi',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->category_id,
        //     ],
        // ]);

        // $parent = CatalogsServicesItem::where('name', 'Механические ТНВД рядного типа')
        //     ->first([
        //         'id',
        //         'slug',
        //         'category_id'
        //     ]);

        // CatalogsServicesItem::insert([
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Bosch',
        //         'slug' => $parent->slug . '/' . \Str::slug('Bosch'),
        //         'level' => 3,
        //         'title' => 'Bosch',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->category_id,
        //     ],
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Denso',
        //         'slug' => $parent->slug . '/' . \Str::slug('Denso'),
        //         'level' => 3,
        //         'title' => 'Denso',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->category_id,
        //     ],
        //     [
        //         'catalogs_service_id' => 1,
        //         'name' => 'Zexel',
        //         'slug' => $parent->slug . '/' . \Str::slug('Zexel'),
        //         'level' => 3,
        //         'title' => 'Zexel',
        //         'company_id' => 1,
        //         'display' => true,
        //         'author_id' => 4,
        //         'parent_id' => $parent->id,
        //         'category_id' => $parent->category_id,
        //     ],
        // ]);

       DB::table('catalogs_service_site')->insert([
           [
               'catalogs_service_id' => 1,
               'site_id' => 2,
           ],
       ]);

        ServicesCategory::insert([
            [
                'name' => 'Байкал',
                'slug' => \Str::slug('Байкал'),
                'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Алтай',
                'slug' => \Str::slug('Алтай'),
                'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Монголия',
                'slug' => \Str::slug('Монголия'),
                'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        // ToolsCategory::insert([
        //     [
        //         'name' => 'Проверка и регулировка',
        //         'slug' => \Str::slug('Проверка и регулировка'),
        //         'level' => 1,
        //         'company_id' => 1,
        //         'author_id' => 4,
        //         'display' => true,
        //     ],
        //     [
        //         'name' => 'Диагностика',
        //         'slug' => \Str::slug('Диагностика'),
        //         'level' => 1,
        //         'company_id' => 1,
        //         'author_id' => 4,
        //         'display' => true,
        //     ],
        // ]);

        // Производители
        // Company::insert([
        //     [
        //         'name' => 'Bosch',
        //         'alias' => \Str::slug('Bosch'),
        //         'location_id' => 1,
        //         'legal_form_id' => 1,
        //         'sector_id' => 23,
        //         'author_id' => 4
        //     ],
        //     [
        //         'name' => 'Denso',
        //         'alias' => \Str::slug('Denso'),
        //         'location_id' => 1,
        //         'legal_form_id' => 1,
        //         'sector_id' => 23,
        //         'author_id' => 4
        //     ],
        // ]);

        // Manufacturer::insert([
        //     [
        //         'company_id' => 1,
        //         'manufacturer_id' => 1
        //     ],
        //     [
        //         'company_id' => 1,
        //         'manufacturer_id' => 2
        //     ],
        //     [
        //         'company_id' => 1,
        //         'manufacturer_id' => 3
        //     ],
        // ]);

        // Supplier::insert([
        //     [
        //         'company_id' => 1,
        //         'supplier_id' => 2
        //     ],
        //     [
        //         'company_id' => 1,
        //         'supplier_id' => 3
        //     ],
        // ]);

        // Vendor::insert([
        //     [
        //         'company_id' => 1,
        //         'supplier_id' => 1,
        //         'status' => 'Компания Виан-Дизель является официальным сервис-дилером DENSO по диагностике и ремонту распределительных ТНВД серии V3/V3ROM/V4'
        //     ],
        //     [
        //         'company_id' => 1,
        //         'supplier_id' => 2,
        //         'status' => 'С 2014г. Виан-Дизель является дизель-сервисом фирмы DELPHI по ремонту инжекторов COMMON RAIL и насос-форсунок'
        //     ],
        // ]);

        // $manufacturers = Manufacturer::where('id', '!=', 1)->get();
        // foreach ($manufacturers as $manufacturer) {
        //     $manufacturer->company->phones()->attach(1, ['main' => 1]);
        // }

        ArticlesGroup::insert([
            [
                'name' => 'Основной склад',
                'units_category_id' => 6,
                'company_id' => 1,
                'author_id' => 4,
            ],
        ]);

        RoomsCategory::insert([
            [
                'name' => 'Основные',
                'slug' => \Str::slug('Основные'),
                'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        \DB::table('articles_group_entity')->insert([
            [
                'articles_group_id' => 1,
                'entity_type' => 'App\RoomsCategory',
                'entity_id' => 1
            ],
        ]);

        Article::insert([
            [
                'name' => 'Основной склад',
                'articles_group_id' => 1,
                'unit_weight_id' => 7,
                'unit_volume_id' => 30,
                'unit_id' => 32,
                'company_id' => 1,
                'author_id' => 4,
                'draft' => 0,
                'display' => 1
            ],
        ]);

        Room::insert([
            [
                'article_id' => 1,
                'category_id' => 1,
                'price_unit_category_id' => 6,
                'price_unit_id' => 32,
                'location_id' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'archive' => 0,
                'display' => 1
            ],
        ]);

        Stock::insert([
            [
                'name' => 'Основной',
                'room_id' => 1,
                'filial_id' => 1,
                'company_id' => 1,
                'author_id' => 4,
            ],
        ]);

    }
}
