<?php

namespace App\Http\Controllers\System;

use App\Action;
use App\ActionEntity;
use App\Entity;
use App\Http\Controllers\Controller;
use App\Menu;
use App\Page;
use App\PhotoSetting;
use App\Position;
use App\Right;
use App\Role;
use App\User;
use DB;

class UpdateController extends Controller
{

    /**
     * UpdateController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Обновление 210420
     *
     * Переименование миграций, т.к. клиенты стали ссылаться на источник
     *
     */
    public function update()
    {
        \DB::table('migrations')->where('migration', '2018_07_31_100846_create_clients_table')->update([
            'migration' => '2018_08_16_040846_create_clients_table'
        ]);
        echo "В миграциях переименована таблица клиентов<br>";

        \DB::table('migrations')->where('migration', '2018_08_01_000000_create_dealers_table')->update([
            'migration' => '2018_08_16_050000_create_dealers_table'
        ]);
        echo "В миграциях переименована таблица дилеров<br><br>";


        echo "<strong>Обновление 21.04.20 завершено</strong>";
    }

    /**
     * Обновление 130420
     *
     * Приведение проектов к единому виду, проставление компании должностям и ролям, генерация имени юзера, добавление сущностей
     */
    public function update_130420()
    {
        Page::where([
            'site_id' => 2,
            'alias' => 'catalogs-goods'
        ])
            ->update([
               'alias' => 'catalogs-goods-items'
            ]);
        echo "Переимеенован алиас страницы раздела каталога<br><br>";

        Position::whereNull('company_id')
            ->update([
                'company_id' => 1
            ]);
        echo "Проставлена компания должностям<br><br>";

        $page_id = Page::where('alias', 'dashboard')->value('id');
        $positions = Position::where('system', false)->update([
            'page_id' => $page_id
        ]);

        echo "Должностям проставлен dashboard<br><br>";

        Role::whereNull('company_id')
            ->where('id', '!=', 1)
            ->update([
                'company_id' => 1
            ]);
        echo "Проставлена компания ролям, кроме роли полный доступ<br><br>";

        $users = User::whereNull('name')
            ->get();

        foreach($users as $user) {
            $cur_user = $user;
            $cur_user->name = $cur_user->first_name . ' ' . $cur_user->second_name;
//            dd($user);
            $cur_user->save();
        }

        echo "Пользователям проставлено поле name<br><br>";

        PhotoSetting::where('id', 1)->update([
            'img_max_size' => 12000,
            'img_formats' => 'jpeg,jpg,png,gif,svg,webp'
        ]);
        echo "Внесены правки в photo_settings<br><br>";

        Page::insert([
            [
                'name' => 'Вендоры',
                'site_id' => 1,
                'title' => 'Вендоры',
                'description' => 'Вендоры',
                'alias' => 'vendors',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Настройки',
                'site_id' => 1,
                'title' => 'Настройки',
                'description' => 'Настройки',
                'alias' => 'settings',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Портфолио',
                'site_id' => 1,
                'title' => 'Портфолио',
                'description' => 'Портфолио',
                'alias' => 'portfolios',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Разделы портфолио',
                'site_id' => 1,
                'title' => 'Разделы портфолио',
                'description' => 'Разделы портфолио',
                'alias' => 'portfolios_items',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Кейсы',
                'site_id' => 1,
                'title' => 'Кейсы',
                'description' => 'Кейсы',
                'alias' => 'business_cases',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Категории выполненных работ',
                'site_id' => 1,
                'title' => 'Категории выполненных работ',
                'description' => 'Категории выполненных работ',
                'alias' => 'outcomes_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Выполненные работы',
                'site_id' => 1,
                'title' => 'Выполненные работы',
                'description' => 'Выполненные работы',
                'alias' => 'outcomes',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);
        echo "Добавлены страницы:<br>Вендоры<br>Настройки<br>Портфолио<br>Разделы портфолио<br>Кейсы<br>Категории выполненных работ<br>Выполненные работы<br><br>";

        $pages = Page::get();
        $menus = Menu::get();

        Menu::insert([
            [
                'name' => 'Вендоры',
                'icon' => null,
                'alias' => 'admin/vendors',
                'tag' => 'vendors',
                'parent_id' => $menus->where('tag', 'guides')->first()->id,
                'page_id' => $pages->where('alias', 'vendors')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],
            [
                'name' => 'Портфолио',
                'icon' => null,
                'alias' => 'admin/portfolios',
                'tag' => 'portfolios',
                'parent_id' => $menus->where('tag', 'marketings')->first()->id,
                'page_id' => $pages->where('alias', 'portfolios')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
            [
                'name' => 'Категории выполненных работ',
                'icon' => null,
                'alias' => 'admin/outcomes_categories',
                'tag' => 'outcomes_categories',
                'parent_id' => $menus->where('tag', 'productions')->first()->id,
                'page_id' => $pages->where('alias', 'outcomes_categories')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 4,
            ],
            [
                'name' => 'Выполненные работы',
                'icon' => null,
                'alias' => 'admin/outcomes',
                'tag' => 'outcomes',
                'parent_id' => $menus->where('tag', 'productions')->first()->id,
                'page_id' => $pages->where('alias', 'outcomes')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
        ]);
        echo "Добавлены пункты меню:<br>Вендоры<br>Портфолио<br>Категории выполненных работ<br>Выполненные работы<br><br>";

        $entities = Entity::get([
            'id',
            'alias',
        ]);
        Entity::insert([
            [
                'name' => 'Вендоры',
                'alias' => 'vendors',
                'model' => 'Vendor',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => $entities->firstWhere('alias', 'companies')->id,
                'view_path' => 'vendors',
                'page_id' => $pages->firstWhere('alias', 'vendors')->id,
            ],
            [
                'name' => 'Настройки',
                'alias' => 'settings',
                'model' => 'Setting',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'system/pages/settings',
                'page_id' => $pages->firstWhere('alias', 'settings')->id,
            ],
            [
                'name' => 'Портфолио',
                'alias' => 'portfolios',
                'model' => 'Portfolio',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'system.pages.portfolios',
                'page_id' => $pages->firstWhere('alias', 'portfolios')->id,
            ],
            [
                'name' => 'Категории выполненных работ',
                'alias' => 'outcomes_categories',
                'model' => 'OutcomesCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'system.pages.outcomes_categories',
                'page_id' => $pages->firstWhere('alias', 'outcomes_categories')->id,
            ],
        ]);
        echo 'Добавлены сущности 1 уровня:<br>Вендоры<br>Настройки<br>Портфолио<br>Категории выполненных работ<br><br>';

        $entities = Entity::get([
            'id',
            'alias',
        ]);
        Entity::insert([
            [
                'name' => 'Разделы портфолио',
                'alias' => 'portfolios_items',
                'model' => 'PortfoliosItem',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => $entities->firstWhere('alias', 'portfolios')->id,
                'view_path' => 'system.pages.portfolios_items',
                'page_id' => $pages->firstWhere('alias', 'portfolios_items')->id,
            ],
            [
                'name' => 'Выполненные работы',
                'alias' => 'outcomes',
                'model' => 'Outcome',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => $entities->firstWhere('alias', 'outcomes_categories')->id,
                'view_path' => 'system.pages.outcomes',
                'page_id' => $pages->firstWhere('alias', 'outcomes')->id,
            ],
        ]);
        echo 'Добавлены сущности 2 уровня:<br>Разделы портфолио<br>Выполненные работы<br><br>';

        $entities = Entity::get([
            'id',
            'alias',
        ]);
        Entity::insert([
            [
                'name' => 'Кейсы',
                'alias' => 'business_cases',
                'model' => 'BusinessCase',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => $entities->firstWhere('alias', 'portfolios_items')->id,
                'view_path' => 'system.pages.business_cases',
                'page_id' => $pages->firstWhere('alias', 'business_cases')->id,
            ],
        ]);
        echo 'Добавлены сущности 3 уровня:<br>Кейсы<br><br>';

        // Наваливание прав
        $entities = Entity::whereIn('alias', [
            'vendors',
            'settings',
            'portfolios',
            'portfolios_items',
            'business_cases',
            'outcomes_categories',
            'outcomes',
        ])
            ->get();

        foreach($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach($actions as $action){
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach($actionentities as $actionentity){

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach($rights as $right){
                $mass[] = [
                    'right_id' => $right->id,
                    'role_id' => 1,
                    'system' => 1
                ];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Добавлены права на сущности:<br>vendors<br>settings<br>portfolios<br>portfolios_items<br>business_cases<br>outcomes_categories<br>outcomes<br><br>";

        echo "<strong>Обновление 13.04.20 завершено</strong>";
    }

    /**
     * Обновление Вкусняшки
     */
    public function update_vkusnyashka()
    {
        Page::insert([
            [
                'name' => 'Каталог товаров',
                'site_id' => 2,
                'title' => 'Все товары',
                'description' => 'Все товары',
                'alias' => 'catalogs-goods',
                'company_id' => 1,
                'author_id' => 4,
                'display' => true,
            ],
        ]);
        echo "Добавлена страница для сайта Вкусняшки - каталог товаров<br><br>";

        echo "<strong>Обновление завершено</strong>";
    }
}
