<?php

use Illuminate\Database\Seeder;

use App\Region;
use App\City;

use App\Company;
use App\Location;
use App\Department;
use App\User;
use App\Staffer;
use App\Employee;
use App\Manufacturer;

use App\Site;
use App\Page;
use App\Navigation;
use App\Menu;

use App\Catalog;
use App\CatalogsItem;

use App\GoodsCategory;
use App\RawsCategory;

use Carbon\Carbon;

class StartTableSeeder extends Seeder
{

    public function run()
    {

    	// Город
    	Region::insert([
        	[
        		'name' => 'Тестовая область',
        		'system_item' => 1,
        		'author_id' => 1,
        	]
        ]);

        City::insert([
        	[
        		'name' => 'Тестовый',
        		'alias' => 'test',
        		'region_id' => 1,
        		'system_item' => 1,
        		'author_id' => 1,
        	]
        ]);

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
        		'name' => 'Иркуцкий',
        		'company_id' => 1,
        		'location_id' => 1,
                'display' => 1,
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
                'company_id' => null,
                'filial_id' => 1,
                'company_id' => 1,
                'god' => null,
                'system_item' => null,
                'author_id' => null,
                'moderation' => null,
                'sex' => 1,
        	]
        ]);

        User::where('login', 'makc_berluskone')->update(['company_id' => 1]);

        $user = User::where('login', 'testovik')->first();
        $user->phones()->attach(1, ['main' => 1]);

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
                'role_id' => 1,
                'department_id' => 1,
                'position_id' => 1,
                'user_id' => User::where('login', 'testovik')->first(['id'])->id,
            ],
        ]);

        Site::insert([
        	[
		        'name' => 'Сайт',
		        'domain' => 'site.ru',
                'alias' => 'site',
		        'company_id' => 1,
                'author_id' => 4,
                'system_item' => null,
                'moderation' => null,
                'api_token' => str_random(60),
        	],
        ]);

        Page::insert([
        	[
                'name' => 'Первая',
                'site_id' => 2,
                'title' => 'Первая',
                'description' => 'Первая',
                'alias' => 'first',
                'company_id' => 1,
                'system_item' => null,
                'author_id' => 4,
                'display' => 1,
            ],
            [
                'name' => 'Вторая',
                'site_id' => 2,
                'title' => 'Вторая',
                'description' => 'Вторая',
                'alias' => 'second',
                'company_id' => 1,
                'system_item' => null,
                'author_id' => 4,
                'display' => 1,
            ],
            [
                'name' => 'Третья',
                'site_id' => 2,
                'title' => 'Третья',
                'description' => 'Третья',
                'alias' => 'third',
                'company_id' => 1,
                'system_item' => null,
                'author_id' => 4,
                'display' => 1,
            ],
        ]);

        Navigation::insert([
        	[
                'name' => 'Главная',
                'site_id' => 2,
                'company_id' => 1,
                'author_id' => 4,
                'display' => 1,
                'align_id' => 1
            ],
        ]);

        Menu::insert([
        	[
                'name' => 'Пункт 1',
                'navigation_id' => 2,
                'company_id' => 1,
                'author_id' => 4,
                'display' => 1,
                'tag' => 'punkt-1',
                'page_id' => Page::where(['site_id' => 2, 'alias' => 'first'])->first()->id,
            ],
            [
                'name' => 'Пункт 2',
                'navigation_id' => 2,
                'company_id' => 1,
                'author_id' => 4,
                'display' => 1,
                'tag' => 'punkt-1',
                'page_id' => Page::where(['site_id' => 2, 'alias' => 'second'])->first()->id,
            ],
            [
                'name' => 'Пункт 3',
                'navigation_id' => 2,
                'company_id' => 1,
                'author_id' => 4,
                'display' => 1,
                'tag' => 'punkt-1',
                'page_id' => Page::where(['site_id' => 2, 'alias' => 'third'])->first()->id,
            ],
        ]);

        Catalog::insert([
            [
                'name' => 'Первый каталог',
                'description' => 'Тест',
                'company_id' => 1,
                'author_id' => 4,
                'display' => 1,
            ],
        ]);

        CatalogsItem::insert([
            [
                'catalog_id' => 1,
                'name' => 'Первый',
                'company_id' => 1,
                'display' => 1,
                'author_id' => 4

            ],
            [
                'catalog_id' => 1,
                'name' => 'Второй',
                'company_id' => 1,
                'display' => 1,
                'author_id' => 4

            ],
            [
                'catalog_id' => 1,
                'name' => 'Третий',
                'company_id' => 1,
                'display' => 1,
                'author_id' => 4

            ],
        ]);

        DB::table('catalog_site')->insert([
            [
                'catalog_id' => 1,
                'site_id' => 2,
            ],
        ]);

        GoodsCategory::insert([
            [
                'name' => 'Первая категория товаров',
                'company_id' => 1,
                'author_id' => 4,
                'display' => 1,
            ],
            [
                'name' => 'Вторая категория товаров',
                'company_id' => 1,
                'author_id' => 4,
                'display' => 1,
            ],
        ]);

        RawsCategory::insert([
            [
                'name' => 'Первая категория сырья',
                'company_id' => 1,
                'author_id' => 4,
                'display' => 1,
            ],
            [
                'name' => 'Вторая категория сырья',
                'company_id' => 1,
                'author_id' => 4,
                'display' => 1,
            ],
        ]);

        // Производители
        Company::insert([
            [
                'name' => 'Первый',
                'alias' => str_slug('Первый'),
                'location_id' => 1,
                'legal_form_id' => 1,
                'sector_id' => 23,
                'author_id' => 4
            ],
            [
                'name' => 'Второй',
                'alias' => str_slug('Второй'),
                'location_id' => 1,
                'legal_form_id' => 1,
                'sector_id' => 23,
                'author_id' => 4
            ],
        ]);

        Manufacturer::insert([
            [
                'company_id' => 1,
                'manufacturer_id' => 2
            ],
            [
                'company_id' => 1,
                'manufacturer_id' => 3
            ],
        ]);

        $manufacturers = Manufacturer::get();
        foreach ($manufacturers as $manufacturer) {
            $manufacturer->company->phones()->attach(1, ['main' => 1]);
        }

    }
}
