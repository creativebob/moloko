<?php

use App\Right;
use Illuminate\Database\Seeder;

use App\Company;
use App\Location;
use App\Department;
use App\User;
use App\Staffer;
use App\Employee;
use App\Manufacturer;
use App\Supplier;

use App\Site;
use App\Domain;
use App\Page;
use App\Navigation;
use App\Menu;

use App\CatalogsGoods;
use App\CatalogsGoodsItem;
use App\CatalogsService;
use App\CatalogsServicesItem;

use App\GoodsCategory;
use App\RawsCategory;
use App\ContainersCategory;
use App\AttachmentsCategory;
use App\RoomsCategory;

use App\ServicesCategory;
use App\WorkflowsCategory;

use App\Position;
use App\Role;

use Carbon\Carbon;

class StartTableSeeder extends Seeder
{

    public function run()
    {

        Location::insert([
        	[
        		'address' => 'Кекная, 11',
        		'city_id' => 1,
        		'country_id' => 1,
        		'author_id' => 1,
        		'parse_count' => 0,
        		'answer_count' => 10
        	]
        ]);


        // Компания
        Company::insert([
        	[
        		'name' => 'Контора',
        		'alias' => 'kontora',
        		'location_id' => 1,
        		'legal_form_id' => 1,
        		'sector_id' => 23,
        		'author_id' => 3
        	]
        ]);

        $company = Company::first();
        $company->phones()->attach(1, ['main' => 1]);

        Department::insert([
        	[
        		'name' => 'Иркутский',
        		'company_id' => 1,
        		'location_id' => 1,
                'display' => true,
        		'author_id' => 3
        	]
        ]);

        $department = Department::first();
        $department->phones()->attach(1, ['main' => 1]);

        User::insert([
        	[
        		'login' => 'testovik',
                'email' => 'lol@mail.ru',
                'password' => bcrypt('123123'),
                'nickname' => 'testovik',
                'first_name' => 'Сотрудник',
                'second_name' => 'Первый',
                'location_id' => 1,
                'user_type' => 1,
                'access_block' => 0,
                'filial_id' => 1,
                'site_id' => 1,
                'company_id' => 1,
                'god' => null,
                'name' => 'Сотрудник Первый',
                'system' => false,
                'author_id' => null,
                'moderation' => false,
                'sex' => 1,
        	]
        ]);

        User::where('login', 'makc_berluskone')->update(['company_id' => 1]);
        User::where('login', 'creativebob')->update(['company_id' => 1]);

        $user = User::where('login', 'testovik')->first();
        $user->phones()->attach(1, ['main' => 1]);

        Position::insert([
            [
                'name' => 'Директор',
                'page_id' => 12,
                'direction' => true,
                'company_id' => 1,
                'system' => false,
                'author_id' => 1,
                'sector_id' => null,
            ],
        ]);

        Role::insert([
            [
                'name' => 'Директор',
                'company_id' => 1,
                'system' => false,
                'author_id' => 1
            ],
        ]);

        $rights = Right::where('directive', 'allow')->get();
        // $rights = Right::get();
        $mass = [];

        // Генерируем права на полный доступ
        foreach($rights as $right) {
            $mass[] = ['right_id' => $right->id, 'role_id' => 2, 'system' => 1];
        };
        DB::table('right_role')->insert($mass);


        DB::table('position_role')->insert([
            [
                'position_id' => 1,
                'role_id' => 2
            ],
        ]);

        Staffer::insert([
            [
                'company_id' => 1,
                'position_id' => 1,
                'department_id' => 1,
                'filial_id' => 1,
                'author_id' => 1,
                'user_id' => User::where('login', 'testovik')->first(['id'])->id,
            ],
        ]);

        Employee::insert([
            [
                'staffer_id' => Staffer::first(['id'])->id,
                'user_id' => User::where('login', 'testovik')->first(['id'])->id,
                'employment_date' => Carbon::today(),
                'company_id' => 1,
                'author_id' => 1,

            ],
        ]);

        DB::table('role_user')->insert([
            [
                'role_id' => 2,
                'department_id' => 1,
                'position_id' => 1,
                'user_id' => User::where('login', 'testovik')->first(['id'])->id,
            ],
        ]);

        Site::insert([
        	[
		        'name' => 'Сайт',
                'alias' => 'site',
		        'company_id' => 1,
                'author_id' => 4,
                'system' => false,
                'moderation' => false,
                'api_token' => \Str::random(60),
        	],
        ]);

        Domain::insert([
            'domain' => 'crmsystem.local',
            'site_id' => 1
        ]);

        Page::insert([
        	[
                'name' => 'Первая',
                'site_id' => 2,
                'title' => 'Первая',
                'description' => 'Первая',
                'alias' => 'first',
                'company_id' => 1,
                'system' => false,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Вторая',
                'site_id' => 2,
                'title' => 'Вторая',
                'description' => 'Вторая',
                'alias' => 'second',
                'company_id' => 1,
                'system' => false,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Третья',
                'site_id' => 2,
                'title' => 'Третья',
                'description' => 'Третья',
                'alias' => 'third',
                'company_id' => 1,
                'system' => false,
                'author_id' => 4,
                'display' => true,
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
                'name' => 'Пункт 1',
                'navigation_id' => 2,
                'slug' => \Str::slug('Пункт 1'),
                'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'tag' => 'punkt-1',
                'page_id' => Page::where(['site_id' => 2, 'alias' => 'first'])->first()->id,
            ],
            [
                'name' => 'Пункт 2',
                'navigation_id' => 2,
                'slug' => \Str::slug('Пункт 2'),
                'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'tag' => 'punkt-1',
                'page_id' => Page::where(['site_id' => 2, 'alias' => 'second'])->first()->id,
            ],
            [
                'name' => 'Пункт 3',
                'navigation_id' => 2,
                'slug' => \Str::slug('Пункт 3'),
                'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
                'tag' => 'punkt-1',
                'page_id' => Page::where(['site_id' => 2, 'alias' => 'third'])->first()->id,
            ],
        ]);


        CatalogsGoods::insert([
            [
                'name' => 'Первый каталог товаров',
                'description' => 'Тест',
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        CatalogsGoodsItem::insert([
            [
                'catalogs_goods_id' => 1,
                'name' => 'Первый',
	            'slug' => \Str::slug('Первый'),
	            'level' => 1,
	            'company_id' => 1,
                'display' => true,
                'author_id' => 4

            ],
            [
                'catalogs_goods_id' => 1,
                'name' => 'Второй',
	            'slug' => \Str::slug('Второй'),
	            'level' => 1,
	            'company_id' => 1,
                'display' => true,
                'author_id' => 4

            ],
            [
                'catalogs_goods_id' => 1,
                'name' => 'Третий',
	            'slug' => \Str::slug('Третий'),
	            'level' => 1,
	            'company_id' => 1,
                'display' => true,
                'author_id' => 4

            ],
        ]);

        DB::table('catalogs_goods_filial')->insert([
            [
                'catalogs_goods_id' => 1,
                'filial_id' => 1,
            ],
        ]);

        CatalogsService::insert([
            [
                'name' => 'Первый каталог услуг',
                'description' => 'Тест',
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        CatalogsServicesItem::insert([
            [
                'catalogs_service_id' => 1,
                'name' => 'Первый',
	            'slug' => \Str::slug('Первый'),
	            'level' => 1,
                'company_id' => 1,
                'display' => true,
                'author_id' => 4

            ],
            [
                'catalogs_service_id' => 1,
                'name' => 'Второй',
	            'slug' => \Str::slug('Второй'),
	            'level' => 1,
                'company_id' => 1,
                'display' => true,
                'author_id' => 4

            ],
            [
                'catalogs_service_id' => 1,
                'name' => 'Третий',
	            'slug' => \Str::slug('Третий'),
	            'level' => 1,
                'company_id' => 1,
                'display' => true,
                'author_id' => 4

            ],
        ]);

        DB::table('catalogs_service_filial')->insert([
            [
                'catalogs_service_id' => 1,
                'filial_id' => 1,
            ],
        ]);

        GoodsCategory::insert([
            [
                'name' => 'Первая категория товаров',
	            'slug' => \Str::slug('Первая категория товаров'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Вторая категория товаров',
	            'slug' => \Str::slug('Вторая категория товаров'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        RawsCategory::insert([
            [
                'name' => 'Первая категория сырья',
	            'slug' => \Str::slug('Первая категория сырья'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Вторая категория сырья',
	            'slug' => \Str::slug('Вторая категория сырья'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        ContainersCategory::insert([
            [
                'name' => 'Первая категория упаковок',
	            'slug' => \Str::slug('Первая категория упаковок'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Вторая категория упаковок',
	            'slug' => \Str::slug('Вторая категория упаковок'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        AttachmentsCategory::insert([
            [
                'name' => 'Первая категория вложений',
                'slug' => \Str::slug('Первая категория вложений'),
                'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Вторая категория вложений',
                'slug' => \Str::slug('Вторая категория вложений'),
                'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        RoomsCategory::insert([
            [
                'name' => 'Первая категория помещений',
	            'slug' => \Str::slug('Первая категория помещений'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Вторая категория помещений',
	            'slug' => \Str::slug('Вторая категория помещений'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        ServicesCategory::insert([
            [
                'name' => 'Первая категория услуг',
	            'slug' => \Str::slug('Первая категория услуг'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Вторая категория услуг',
	            'slug' => \Str::slug('Вторая категория услуг'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],

            [
                'name' => 'Первая общая категория услуг',
	            'slug' => \Str::slug('Первая общая категория услуг'),
	            'level' => 1,
                'company_id' => null,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Вторая общая категория услуг',
	            'slug' => \Str::slug('Вторая общая категория услуг'),
	            'level' => 1,
                'company_id' => null,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        WorkflowsCategory::insert([
            [
                'name' => 'Первая категория рабочих процессов',
	            'slug' => \Str::slug('Первая категория рабочих процессов'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
            [
                'name' => 'Вторая категория рабочих процессов',
	            'slug' => \Str::slug('Вторая категория рабочих процессов'),
	            'level' => 1,
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);

        // Производители
        Company::insert([
            [
                'name' => 'Первый',
                'alias' => \Str::slug('Первый'),
                'location_id' => 1,
                'legal_form_id' => 1,
                'sector_id' => 23,
                'author_id' => 4
            ],
            [
                'name' => 'Второй',
                'alias' => \Str::slug('Второй'),
                'location_id' => 1,
                'legal_form_id' => 1,
                'sector_id' => 23,
                'author_id' => 4
            ],
        ]);

        Manufacturer::insert([
            [
                'company_id' => 1,
                'manufacturer_id' => 1
            ],
            [
                'company_id' => 1,
                'manufacturer_id' => 2
            ],
            [
                'company_id' => 1,
                'manufacturer_id' => 3
            ],
        ]);

        $manufacturers = Manufacturer::where('id', '!=', 1)
            ->get();
        foreach ($manufacturers as $manufacturer) {
            $manufacturer->company->phones()->attach(1, ['main' => 1]);
        }

        Supplier::insert([
            [
                'company_id' => 1,
                'supplier_id' => 2
            ],
            [
                'company_id' => 1,
                'supplier_id' => 3
            ],
        ]);

    }
}
