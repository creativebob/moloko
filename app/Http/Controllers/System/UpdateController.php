<?php

namespace App\Http\Controllers\System;

use App\Action;
use App\ActionEntity;
use App\Channel;
use App\Charge;
use App\CompaniesSetting;
use App\CompaniesSettingsCategory;
use App\Entity;
use App\Models\System\Documents\EstimatesGoodsItem;
use App\Http\Controllers\Controller;
use App\Menu;
use App\Notification;
use App\OutletsSetting;
use App\OutletsSettingsCategory;
use App\Page;
use App\PhotoSetting;
use App\Position;
use App\Right;
use App\Role;
use App\Sector;
use App\TemplatesCategory;
use App\Trigger;
use App\User;
use App\Widget;
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
     * Добавление сущности файлов
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function addFilesEntity()
    {
        $entity = Entity::where('alias', 'files')
            ->exists();

        if (!$entity) {
            Entity::insert([
                'name' => 'Файлы',
                'alias' => 'files',
                'model' => 'App\File',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => null,
                'page_id' => null,
            ]);
            echo 'Добавлена сущность<br><br>';

            // Наваливание прав
            $entities = Entity::where('alias', 'files')
                ->get();

            foreach ($entities as $entity) {
                // Генерируем права
                $actions = Action::get();
                $mass = [];

                foreach ($actions as $action) {
                    $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
                };
                DB::table('action_entity')->insert($mass);

                $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
                $mass = [];

                foreach ($actionentities as $actionentity) {

                    $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                    $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
                };

                DB::table('rights')->insert($mass);

                $actionentities = $actionentities->pluck('id')->toArray();

                // Получаем все существующие разрешения (allow)
                $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

                $mass = [];
                // Генерируем права на полный доступ
                foreach ($rights as $right) {
                    $mass[] = [
                        'right_id' => $right->id,
                        'role_id' => 1,
                        'system' => 1
                    ];
                };

                DB::table('right_role')->insert($mass);
            }

            echo "Добавлены права на сущность<br><br>";
        }

        return __('msg.ok');
    }

    /**
     * Добавление сущности плагинов
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function addPluginsEntity()
    {

        $entity = Entity::where('alias', 'plugins')
            ->exists();

        if (!$entity) {
            Entity::insert([
                'name' => 'Плагины',
                'alias' => 'plugins',
                'model' => 'App\Plugin',
                'rights' => false,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => null,
                'page_id' => null,
            ]);
        }

        return __('msg.ok');
    }

    /**
     * Обновление секторов
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function sectors()
    {
        // Первй раздел
        $items = [
            [
                'name' => 'Сельское хозяйство',
                'author_id' => 1,
                'tag' => \Str::slug('Сельское хозяйство'),
            ],
        ];

        $count = 0;
        foreach ($items as $item) {
            $res = Sector::where($item)
                ->exists();
//            dd($res);

            if (!$res) {
                Sector::insert($item);
                $count++;
            }
        }

        // Вложенные
        $parentSectors = Sector::whereNull('parent_id')
            ->get();

        $items = [
            [
                'name' => 'Сервис ремонта',
                'parent_id' => $parentSectors->firstWhere('name', 'Транспорт')->id,
                'author_id' => 1,
                'tag' => \Str::slug('Сервис ремонта'),
                'category_id' => $parentSectors->firstWhere('name', 'Транспорт')->id,
            ],
            [
                'name' => 'Производство и переработка мяса',
                'parent_id' => $parentSectors->firstWhere('name', 'Сельское хозяйство')->id,
                'author_id' => 1,
                'tag' => \Str::slug('Производство и переработка мяса'),
                'category_id' => $parentSectors->firstWhere('name', 'Сельское хозяйство')->id,
            ],
        ];

        foreach ($items as $item) {
            $res = Sector::where($item)
                ->exists();
//            dd($res);

            if (!$res) {
                Sector::insert($item);
                $count++;
            }
        }

        return __("Добавлено секторов: {$count}");
    }

    /**
     * Обновление блока Email Рассылок в меню
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function emailsMenus()
    {
        $section = Menu::where('tag', 'direct_mail')
            ->exists();

        if (!$section) {
            Menu::insert([
                'name' => 'E-mail рассылки',
                'icon' => null,
                'alias' => null,
                'tag' => 'direct_mail',
                'parent_id' => Menu::where('tag', 'marketings')->value('id'),
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ]);

            $emails = Menu::where('tag', 'direct_mail')
                ->first();

            $items = [
                'subscribers',
                'mailing_lists',
                'mailings'
            ];

            foreach ($items as $item) {
                Menu::where('tag', $item)
                    ->update([
                    'parent_id' => $emails->id
                ]);
            }
        }

        return __('msg.ok');
    }

    /**
     * Добавление в развернутую систему сущности конкурентов с правами
     *
     * @return string
     */
    public function addCompetitorsEntity()
    {
        Page::insert([
            [
                'name' => 'Конкуренты',
                'site_id' => 1,
                'title' => 'Конкуренты',
                'description' => 'Конкуренты',
                'alias' => 'competitors',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);
        echo "Добавлена страница<br><br>";

        $researches = Menu::where('tag', 'researches')
            ->exists();

        if (!$researches) {
            $menus = Menu::get();

            Menu::insert([
                [
                    'name' => 'Исследования',
                    'icon' => null,
                    'alias' => null,
                    'tag' => 'researches',
                    'parent_id' => $menus->where('tag', 'marketings')->first()->id,
                    'page_id' => null,
                    'navigation_id' => 1,
                    'company_id' => null,
                    'system' => true,
                    'author_id' => 1,
                    'display' => true,
                    'sort' => 6,
                ],
            ]);
        }

        $pages = Page::get();
        $menus = Menu::get();

        Menu::insert([
            [
                'name' => 'Конкуренты',
                'icon' => null,
                'alias' => 'admin/competitors',
                'tag' => 'competitors',
                'parent_id' => $menus->firstWhere('tag', 'researches')->id,
                'page_id' => $pages->firstWhere('alias', 'competitors')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],
        ]);
        echo "Добавлен пункт меню<br><br>";

        Entity::insert([
            [
                'name' => 'Конкуренты',
                'alias' => 'competitors',
                'model' => 'App\Competitor',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('companies')->value('id'),
                'view_path' => 'system.pages.marketings.competitors',
                'page_id' => $pages->firstWhere('alias', 'competitors')->id,
            ],
        ]);
        echo 'Добавлена сущность<br><br>';

        // Наваливание прав
        $entities = Entity::where('alias', 'competitors')
            ->get();

        foreach ($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach ($actions as $action) {
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach ($actionentities as $actionentity) {

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach ($rights as $right) {
                $mass[] = [
                    'right_id' => $right->id,
                    'role_id' => 1,
                    'system' => 1
                ];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Добавлены права на сущность<br><br>";

        return "<strong>Добавление сущности меток заказа завершено</strong>";
    }

    /**
     * Добавление в развернутую систему сущности агентов с правами
     *
     * @return string
     */
    public function addAgentsEntity()
    {
        $pageData = [
            'name' => 'Агенты',
            'site_id' => 1,
            'title' => 'Агенты',
            'description' => 'Агенты',
            'alias' => 'agents',
            'company_id' => null,
            'system' => true,
            'author_id' => 1,
            'display' => true,
        ];

        $res = Page::where($pageData)
            ->exists();

        if (!$res) {
            Page::insert($pageData);
            echo "Добавлена страница<br><br>";
        }

        $pages = Page::get();
        $menus = Menu::get();

        $menuData = [
            'name' => 'Агенты',
            'icon' => null,
            'alias' => 'admin/agents',
            'tag' => 'agents',
            'parent_id' => $menus->where('tag', 'sales')->first()->id,
            'page_id' => $pages->where('alias', 'agents')->first()->id,
            'navigation_id' => 1,
            'company_id' => null,
            'system' => true,
            'author_id' => 1,
            'display' => true,
            'sort' => 4,
        ];

        $res = Menu::where($menuData)
            ->exists();

        if (!$res) {
            Menu::insert($menuData);
            echo "Добавлен пункт меню<br><br>";
        }

        $entityData = [
            'name' => 'Агенты',
            'alias' => 'agents',
            'model' => 'App\Agent',
            'rights' => true,
            'system' => true,
            'author_id' => 1,
            'site' => 0,
            'ancestor_id' => Entity::whereAlias('companies')->first(['id'])->id,
            'view_path' => 'system.pages.sales.agents',
            'page_id' => $pages->firstWhere('alias', 'agents')->id,
        ];

        $res = Entity::where($entityData)
            ->exists();

        if (!$res) {
            Entity::insert($entityData);
            echo 'Добавлена сущность<br><br>';

            // Наваливание прав
            $entities = Entity::where('alias', 'agents')
                ->get();

            foreach ($entities as $entity) {
                // Генерируем права
                $actions = Action::get();
                $mass = [];

                foreach ($actions as $action) {
                    $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
                };
                DB::table('action_entity')->insert($mass);

                $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
                $mass = [];

                foreach ($actionentities as $actionentity) {

                    $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                    $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
                };

                DB::table('rights')->insert($mass);

                $actionentities = $actionentities->pluck('id')->toArray();

                // Получаем все существующие разрешения (allow)
                $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

                $mass = [];
                // Генерируем права на полный доступ
                foreach ($rights as $right) {
                    $mass[] = [
                        'right_id' => $right->id,
                        'role_id' => 1,
                        'system' => 1
                    ];
                };

                DB::table('right_role')->insert($mass);
            }

            echo "Добавлены права на сущность<br><br>";
        }

        return "<strong>Добавление сущности агентов завершено</strong>";
    }

    /**
     * Добавление в развернутую систему сущности категорий объектов воздействия с правами
     *
     * @return string
     */
    public function addImpactsEntities()
    {
        Page::insert([
            [
                'name' => 'Категории объектов воздействия',
                'site_id' => 1,
                'title' => 'Категории объектов воздействия',
                'description' => 'Категории объектов воздействия',
                'alias' => 'impacts_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Объекты воздействия',
                'site_id' => 1,
                'title' => 'Объекты воздействия',
                'description' => 'Объекты воздействия',
                'alias' => 'impacts',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Склады объектов воздействия',
                'site_id' => 1,
                'title' => 'Склады объектов воздействия',
                'description' => 'Склады объектов воздействия',
                'alias' => 'impacts_stocks',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);
        echo "Добавлены страницы<br><br>";

        Menu::insert([
            //  Объекты воздействия
            [
                'name' => 'Объекты воздействия',
                'icon' => 'icon-impact',
                'alias' => null,
                'tag' => 'impacts',
                'parent_id' => null,
                'page_id' => null,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 9,
            ],
        ]);

        echo "Добавлен раздел в сайдбаре<br><br>";

        $pages = Page::get();
        $menus = Menu::get();

        Menu::insert([
            [
                'name' => 'Категории объектов воздействия',
                'icon' => null,
                'alias' => 'admin/impacts_categories',
                'tag' => 'impacts_categories',
                'parent_id' => $menus->where('tag', 'impacts')->first()->id,
                'page_id' => $pages->where('alias', 'impacts_categories')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],
            [
                'name' => 'Объекты воздействия',
                'icon' => null,
                'alias' => 'admin/impacts',
                'tag' => 'impacts',
                'parent_id' => $menus->where('tag', 'impacts')->first()->id,
                'page_id' => $pages->where('alias', 'impacts')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],
            [
                'name' => 'Склады объектов воздействия',
                'icon' => null,
                'alias' => 'admin/impacts_stocks',
                'tag' => 'impacts_stocks',
                'parent_id' => $menus->where('tag', 'impacts')->first()->id,
                'page_id' => $pages->where('alias', 'impacts_stocks')->first()->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 1,
            ],
        ]);
        echo "Добавлены пункты меню<br><br>";

        $pages = Page::get();

        Entity::insert([
            [
                'name' => 'Категории объектов воздействия',
                'alias' => 'impacts_categories',
                'model' => 'App\ImpactsCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 1,
                'view_path' => 'products/articles_categories/impacts_categories',
                'page_id' => $pages->firstWhere('alias', 'impacts_categories')->id,
            ],
        ]);
        Entity::insert([
            [
                'name' => 'Объекты воздействия',
                'alias' => 'impacts',
                'model' => 'App\Impact',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('impacts_categories')->value('id'),
                'view_path' => 'products/articles/impacts',
                'page_id' => $pages->firstWhere('alias', 'impacts')->id,
            ],
        ]);

        Entity::insert([
            [
                'name' => 'Склад объектов воздействия',
                'alias' => 'impacts_stocks',
                'model' => 'App\Models\System\Stocks\ImpactsStock',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('impacts')->first(['id'])->id,
                'view_path' => 'system.pages.cmv_stocks.impacts_stocks',
                'page_id' => $pages->firstWhere('alias', 'impacts_stocks')->id,
            ],
        ]);
        echo 'Добавлены сущности<br><br>';

        // Наваливание прав
        $entities = Entity::whereIn('alias', [
            'impacts_categories',
            'impacts',
            'impacts_stocks',
        ])
            ->get();

        foreach ($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach ($actions as $action) {
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach ($actionentities as $actionentity) {

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach ($rights as $right) {
                $mass[] = [
                    'right_id' => $right->id,
                    'role_id' => 1,
                    'system' => 1
                ];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Добавлены права на сущности<br><br>";

        return "<strong>Добавление сущностей объектов воздействия (категории, сущность, хранилища) завершено</strong>";
    }

    /**
     * Добавление в развернутую систему сущности меток заказа с правами
     *
     * @return string
     */
    public function addLabelsEntity()
    {
        Page::insert([
            [
                'name' => 'Метки заказа',
                'site_id' => 1,
                'title' => 'Метки заказа',
                'description' => 'Метки заказа',
                'alias' => 'labels',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);
        echo "Добавлена страница<br><br>";

        $pages = Page::get();
        $menus = Menu::get();

        Menu::insert([
            [
                'name' => 'Метки заказа',
                'icon' => null,
                'alias' => 'admin/labels',
                'tag' => 'labels',
                'parent_id' => $menus->firstWhere('tag', 'guides')->id,
                'page_id' => $pages->firstWhere('alias', 'labels')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => null,
            ],
        ]);
        echo "Добавлен пункт меню<br><br>";

        $entities = Entity::get([
            'id',
            'alias',
        ]);
        Entity::insert([
            [
                'name' => 'Метки заказа',
                'alias' => 'labels',
                'model' => 'App\Label',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'system.pages.labels',
                'page_id' => $pages->firstWhere('alias', 'labels')->id,
            ],
        ]);
        echo 'Добавлена сущность<br><br>';

        // Наваливание прав
        $entities = Entity::where('alias', 'labels')
            ->get();

        foreach ($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach ($actions as $action) {
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach ($actionentities as $actionentity) {

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach ($rights as $right) {
                $mass[] = [
                    'right_id' => $right->id,
                    'role_id' => 1,
                    'system' => 1
                ];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Добавлены права на сущность<br><br>";

        return "<strong>Добавление сущности меток заказа завершено</strong>";
    }

    /**
     * Добавление в развернутую систему сущности рабочих мест с правами
     *
     * @return string
     */
    public function addWorkplacesEntity()
    {
        Page::insert([
            [
                'name' => 'Рабочие места',
                'site_id' => 1,
                'title' => 'Рабочие места',
                'description' => 'Рабочие места',
                'alias' => 'workplaces',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);
        echo "Добавлена страница<br><br>";

        $pages = Page::get();
        $menus = Menu::get();

        Menu::insert([
            [
                'name' => 'Рабочие места',
                'icon' => null,
                'alias' => 'admin/workplaces',
                'tag' => 'workplaces',
                'parent_id' => $menus->firstWhere('tag', 'sales')->id,
                'page_id' => $pages->firstWhere('alias', 'workplaces')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 7,
            ],
        ]);
        echo "Добавлен пункт меню<br><br>";

        $entities = Entity::get([
            'id',
            'alias',
        ]);
        Entity::insert([
            [
                'name' => 'Рабочие места',
                'alias' => 'workplaces',
                'model' => 'App\Workplace',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'system.pages.workplaces',
                'page_id' => $pages->firstWhere('alias', 'workplaces')->id,
            ],

        ]);
        echo 'Добавлена сущность<br><br>';

        // Наваливание прав
        $entities = Entity::where('alias', 'workplaces')
            ->get();

        foreach ($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach ($actions as $action) {
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach ($actionentities as $actionentity) {

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach ($rights as $right) {
                $mass[] = [
                    'right_id' => $right->id,
                    'role_id' => 1,
                    'system' => 1
                ];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Добавлены права на сущность<br><br>";

        return "<strong>Добавление сущности рабочих мест завершено</strong>";
    }

    /**
     * Обновление триггеров
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function triggers()
    {
        $entities = Entity::get();

        $items = [
            [
                'name' => 'Лид с сайта',
                'alias' => 'create-lead-from-project',
                'description' => null,
                'entity_id' => $entities->firstWhere('alias', 'leads')->id,
            ],
            [
                'name' => 'Рекламация',
                'alias' => 'create-claim',
                'description' => null,
                'entity_id' => $entities->firstWhere('alias', 'claims')->id,
            ],
            [
                'name' => 'Уведомление',
                'alias' => 'notification',
                'description' => null,
                'entity_id' => null,
            ],
            [
                'name' => 'Предложение',
                'alias' => 'offer',
                'description' => null,
                'entity_id' => null,
            ],
            [
                'name' => 'Контроль вкл / выкл скидок',
                'alias' => 'discounts-recalculate',
                'description' => null,
                'entity_id' => $entities->firstWhere('alias', 'discounts')->id,
            ],
        ];

        $count = 0;
        foreach ($items as $item) {
            $res = Trigger::where($item)
                ->exists();
//            dd($res);

            if (!$res) {
                $charge = Trigger::insert($item);
                $count++;
            }
        }
        return __("Добавлено триггеров: {$count}");
    }

    /**
     * Обновление обязанностей
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function charges()
    {
        $items = [
            [
                'name' => 'Просмотр истории лида',
                'description' => null,
                'alias' => 'lead-history',
                'author_id' => 1,
            ],
        ];

        $count = 0;
        foreach ($items as $item) {
            $res = Charge::where($item)
                ->exists();
//            dd($res);

            if (!$res) {
                $charge = Charge::insert($item);
                $count++;
            }
        }
        return __("Добавлено обязанностей: {$count}");
    }

    /**
     * Обновление оповещений
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function notifications()
    {
        $channels = Channel::get();
        $triggers = Trigger::get();

        $items = [
            [
                'name' => 'Лид с сайта',
                'channel_id' => $channels->firstWhere('name', 'Telegram')->id,
                'trigger_id' => $triggers->firstWhere('alias', 'create-lead-from-project')->id,
            ],
            [
                'name' => 'Рекламация',
                'channel_id' => $channels->firstWhere('name', 'Telegram')->id,
                'trigger_id' => $triggers->firstWhere('alias', 'create-claim')->id,
            ],
            [
                'name' => 'Получать СМС уведомления',
                'channel_id' => $channels->firstWhere('name', 'Sms')->id,
                'trigger_id' => $triggers->firstWhere('alias', 'notification')->id,
            ],
            [
                'name' => 'Получать предложения на почту',
                'channel_id' => $channels->firstWhere('name', 'Email')->id,
                'trigger_id' => $triggers->firstWhere('alias', 'offer')->id,
            ],
            [
                'name' => 'Контроль вкл / выкл скидок',
                'channel_id' => $channels->firstWhere('name', 'Telegram')->id,
                'trigger_id' => $triggers->firstWhere('alias', 'discounts-recalculate')->id,
            ],
            [
                'name' => 'Прием заказа от партнера',
                'channel_id' => $channels->firstWhere('name', 'Telegram')->id,
                'trigger_id' => $triggers->firstWhere('alias', 'create-lead-from-project')->id,
            ],
        ];

        $count = 0;
        foreach ($items as $item) {
            $res = Notification::where($item)
                ->exists();
//            dd($res);

            if (!$res) {
                $notiification = Notification::create($item);
                $notiification->sites()->attach(1);

                $count++;
            }
        }
        return __("Добавлено оповещений: {$count}");
    }

    /**
     * Обновление ролей
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function roles()
    {
        $items = [
            [
                'name' => 'Базовая',
                'alias' => 'base',
                'company_id' => null,
                'system' => false,
                'author_id' => 1
            ],

            [
                'name' => 'Агент',
                'alias' => 'agents',
                'company_id' => null,
                'system' => false,
                'author_id' => 1
            ],
            [
                'name' => 'Клиент',
                'alias' => 'clients',
                'company_id' => null,
                'system' => false,
                'author_id' => 1
            ],
            [
                'name' => 'Поставщик',
                'alias' => 'suppliers',
                'company_id' => null,
                'system' => false,
                'author_id' => 1
            ],
            [
                'name' => 'Производитель',
                'alias' => 'manufacturers',
                'company_id' => null,
                'system' => false,
                'author_id' => 1
            ]
        ];

        $count = 0;
        foreach ($items as $item) {
            $res = Role::where($item)
                ->exists();
//            dd($res);

            if (!$res) {
                Role::insert($item);
                $count++;
            }
        }
        return __("Добавлено ролей: {$count}");
    }

    /**
     * Настройка работы с резервами на торговую точку
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function addOutletReservesSetting()
    {
        OutletsSetting::insert([
            [
                'name' => 'Работать с резервами',
                'alias' => 'reserves',
                'category_id' => OutletsSettingsCategory::where('alias', 'others')
                    ->value('id')
            ],
        ]);

        return __('msg.ok');
    }

    /**
     * Настрока работы с агентом на торговую точку
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function addOutletAgentSetting()
    {
        OutletsSetting::insert([
            [
                'name' => 'Работа с агентом',
                'alias' => 'agents',
                'category_id' => OutletsSettingsCategory::where('alias', 'others')
                    ->value('id')
            ],
        ]);

        return __('msg.ok');
    }

    /**
     *
     * Переименование миграций, т.к. пункты сметы стали ссылаться на агентов
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function updateAgentsTablesInMigrationsTable()
    {
        \DB::table('migrations')->where('migration', '2020_11_06_135658_create_agent_types_table')->update([
            'migration' => '2019_06_11_135658_create_agent_types_table'
        ]);

        \DB::table('migrations')->where('migration', '2020_11_07_000000_create_agents_table')->update([
            'migration' => '2019_06_11_140000_create_agents_table'
        ]);

        \DB::table('migrations')->where('migration', '2020_12_12_191046_create_agency_schemes_table')->update([
            'migration' => '2019_06_11_191046_create_agency_schemes_table'
        ]);

        return __('msg.ok');
    }

    /**
     * Настройки авторасчета даты отгрузки и начисления поинтов
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function addLoyaltySettings()
    {

        OutletsSetting::insert([
            [
                'name' => 'Авторасчет времени отгрузки',
                'alias' => 'shipment_at-calculate',
                'category_id' => OutletsSettingsCategory::where('alias', 'others')
                    ->value('id')
            ],
        ]);

        CompaniesSettingsCategory::insert([
            [
                'name' => 'Система лояльности',
                'slug' => \Str::slug('Система лояльности'),
                'level' => 1,
                'alias' => 'loyalty-system',
            ],
        ]);

        CompaniesSetting::insert([
            [
                'name' => 'Начисление поинтов',
                'alias' => 'points-calculate',
                'category_id' => CompaniesSettingsCategory::where('alias', 'loyalty-system')
                    ->value('id')
            ],
        ]);

        return __('msg.ok');
    }

    /**
     * Настройка проверки остатков на складе при продаже на торговой точке
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function addStockOutletSetting()
    {
        $categoryId = OutletsSettingsCategory::where('alias', 'others')
            ->value('id');

        OutletsSetting::insert([
            [
                'name' => 'Проверка остатка на складе при продаже',
                'alias' => 'stock-check-free',
                'category_id' => $categoryId
            ],
        ]);

        return __('msg.ok');
    }

    /**
     * Добавление катеории и настроек на торговую точку
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function addOutletSettings()
    {
        OutletsSettingsCategory::insert([
            [
                'name' => 'Прочие',
                'slug' => \Str::slug('Прочие'),
                'level' => 1,
                'alias' => 'others',
            ],
        ]);

        $categoryId = OutletsSettingsCategory::where('alias', 'others')
            ->value('id');

        OutletsSetting::insert([
            [
                'name' => 'Отображать номиналы',
                'alias' => 'denominations-show',
                'category_id' => $categoryId
            ],
            [
                'name' => 'Автозаполнение суммы',
                'alias' => 'amount-autofill',
                'category_id' => $categoryId
            ],
            [
                'name' => 'Отображение отмененных платежей',
                'alias' => 'canceled-payments-show',
                'category_id' => $categoryId
            ],
        ]);

        $categoryId = OutletsSettingsCategory::where('alias', 'cash-register')
            ->value('id');

        OutletsSetting::insert([
            [
                'name' => 'Выбор даты платежа',
                'alias' => 'payment-date-change',
                'category_id' => $categoryId
            ],
        ]);

        return __('msg.ok');
    }

    /**
     * Добавление категории шаблонов - чеки
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function setChecksTemplatesCategory()
    {
        TemplatesCategory::insert([
            [
                'name' => 'Чеки',
                'slug' => \Str::slug('Чеки'),
                'level' => 1,
                'company_id' => null,
                'author_id' => 1,
                'display' => true,
            ],
        ]);

        return __('msg.ok');
    }

    /**
     * Обновление спецправ на отмену товарок / нарядов
     *
     * @return array|string|null
     */
    public function addCancelCharges()
    {
        Charge::insert([
            [
                'name' => 'Отмена товарной накладной',
                'alias' => 'consignment-cancel',
                'author_id' => 1,
            ],
            [
                'name' => 'Отмена наряда на производство',
                'alias' => 'production-cancel',
                'author_id' => 1,
            ],
        ]);
        return __('msg.ok');
    }

    /**
     * Добавление в развернутую систему сущности торговых точек с правами
     *
     * @return string
     */
    public function addOutletsEntity()
    {
        Page::insert([
            [
                'name' => 'Торговые точки',
                'site_id' => 1,
                'title' => 'Торговые точки',
                'description' => 'Торговые точки',
                'alias' => 'outlets',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);
        echo "Добавлена страница<br><br>";

        $pages = Page::get();
        $menus = Menu::get();

        Menu::insert([
            [
                'name' => 'Торговые точки',
                'icon' => null,
                'alias' => 'admin/outlets',
                'tag' => 'outlets',
                'parent_id' => $menus->firstWhere('tag', 'sales')->id,
                'page_id' => $pages->firstWhere('alias', 'outlets')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 6,
            ],
        ]);
        echo "Добавлен пункт меню<br><br>";

        $entities = Entity::get([
            'id',
            'alias',
        ]);
        Entity::insert([
            [
                'name' => 'Торговые точки',
                'alias' => 'outlets',
                'model' => 'App\Outlet',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'system.pages.outlets',
                'page_id' => $pages->firstWhere('alias', 'outlets')->id,
            ],

        ]);
        echo 'Добавлена сущность<br><br>';

        // Наваливание прав
        $entities = Entity::where('alias', 'outlets')
            ->get();

        foreach ($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach ($actions as $action) {
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach ($actionentities as $actionentity) {

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach ($rights as $right) {
                $mass[] = [
                    'right_id' => $right->id,
                    'role_id' => 1,
                    'system' => 1
                ];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Добавлены права на сущность<br><br>";

        return "<strong>Добавление сущности торговых точек завершено</strong>";
    }

    /**
     * Добавление в развернутую систему сущностей для рассылок с правами
     *
     * @return string
     */
    public function addMailingsEntities()
    {
        // Правим существующую сущность dispatches
        Entity::where('alias', 'dispatches')
            ->update([
                'rights' => false,
            ]);

        $menu = Menu::where('alias', 'admin/dispatches')
            ->first();
        $menu->forceDelete();

        $page = Page::where('alias', 'dispatches')
            ->first();
        $page->forceDelete();


        Page::insert([
            // Рассылки
            [
                'name' => 'Подписчики',
                'site_id' => 1,
                'title' => 'Подписчики',
                'description' => 'Подписчики',
                'alias' => 'subscribers',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Категории шаблонов',
                'site_id' => 1,
                'title' => 'Категории шаблонов',
                'description' => 'Категории шаблонов',
                'alias' => 'templates_categories',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Шаблоны',
                'site_id' => 1,
                'title' => 'Шаблоны',
                'description' => 'Шаблоны',
                'alias' => 'templates',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
            [
                'name' => 'Списки рассылок',
                'site_id' => 1,
                'title' => 'Списки рассылок',
                'description' => 'Списки рассылок',
                'alias' => 'mailing_lists',
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
                'alias' => 'mailings',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);
        echo "Добавлены страницы рассылок<br><br>";

        $pages = Page::get();
        $menus = Menu::get();

        Menu::insert([
            [
                'name' => 'Подписчики',
                'icon' => null,
                'alias' => 'admin/subscribers',
                'tag' => 'subscribers',
                'parent_id' => $menus->firstWhere('tag', 'marketings')->id,
                'page_id' => $pages->firstWhere('alias', 'subscribers')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
            [
                'name' => 'Категории шаблонов',
                'icon' => null,
                'alias' => 'admin/templates_categories',
                'tag' => 'templates_categories',
                'parent_id' => $menus->firstWhere('tag', 'marketings')->id,
                'page_id' => $pages->firstWhere('alias', 'templates_categories')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
            [
                'name' => 'Шаблоны',
                'icon' => null,
                'alias' => 'admin/templates',
                'tag' => 'templates',
                'parent_id' => $menus->firstWhere('tag', 'marketings')->id,
                'page_id' => $pages->firstWhere('alias', 'templates')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
            [
                'name' => 'Списки рассылок',
                'icon' => null,
                'alias' => 'admin/mailing_lists',
                'tag' => 'mailing_lists',
                'parent_id' => $menus->firstWhere('tag', 'marketings')->id,
                'page_id' => $pages->firstWhere('alias', 'mailing_lists')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
            [
                'name' => 'Рассылки',
                'icon' => null,
                'alias' => 'admin/mailings',
                'tag' => 'mailings',
                'parent_id' => $menus->firstWhere('tag', 'marketings')->id,
                'page_id' => $pages->firstWhere('alias', 'mailings')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
        ]);
        echo "Добавлены пункты меню для рассылок<br><br>";

        $entities = Entity::get([
            'id',
            'alias',
        ]);
        Entity::insert([
            [
                'name' => 'Категории шаблонов',
                'alias' => 'templates_categories',
                'model' => 'App\TemplatesCategory',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'templates_categories',
                'page_id' => $pages->firstWhere('alias', 'templates_categories')->id,
            ],
            [
                'name' => 'Подписчики',
                'alias' => 'subscribers',
                'model' => 'App\Subscriber',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'subscribers',
                'page_id' => $pages->firstWhere('alias', 'subscribers')->id,
            ],
            [
                'name' => 'Список для рассылки',
                'alias' => 'mailing_lists',
                'model' => 'App\MailingList',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'mailing_lists',
                'page_id' => $pages->firstWhere('alias', 'mailing_lists')->id,
            ],
            [
                'name' => 'Рассылки',
                'alias' => 'mailings',
                'model' => 'App\Mailing',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'mailings',
                'page_id' => $pages->firstWhere('alias', 'mailings')->id,
            ],
        ]);

        Entity::insert([
            [
                'name' => 'Шаблоны',
                'alias' => 'templates',
                'model' => 'App\Template',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'ancestor_id' => Entity::whereAlias('templates_categories')->value('id'),
                'view_path' => 'templates',
                'page_id' => $pages->firstWhere('alias', 'templates')->id,
            ],
        ]);
        echo 'Добавлены сущности рассылок<br><br>';

        // Наваливание прав
        $entities = Entity::whereIn('alias', [
            'templates_categories',
            'subscribers',
            'mailing_lists',
            'mailings',
            'templates',
        ])
            ->get();

        foreach ($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach ($actions as $action) {
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach ($actionentities as $actionentity) {

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach ($rights as $right) {
                $mass[] = [
                    'right_id' => $right->id,
                    'role_id' => 1,
                    'system' => 1
                ];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Добавлены права на сущности рассылок<br><br>";

        return "<strong>Добавление сущностей рассылок завершено</strong>";
    }

    /**
     * Добавление в развернутую систему оповещения о контроле скидок
     *
     * @return string
     */
    public function addDiscountsRecalculateNotification()
    {
        $entities = Entity::get();

        $trigger = Trigger::firstOrCreate([
            'name' => 'Контроль вкл / выкл скидок',
            'alias' => 'discounts-recalculate',
            'description' => null,
            'entity_id' => $entities->firstWhere('alias', 'discounts')->id,
        ]);

        $channels = Channel::get();
        $triggers = Trigger::get();

        $notiification = Notification::firstOrCreate([
            'name' => 'Контроль вкл / выкл скидок',
            'channel_id' => $channels->firstWhere('name', 'Telegram')->id,
            'trigger_id' => $triggers->firstWhere('alias', 'discounts-recalculate')->id,
        ]);

        $notiification->sites()->attach(1);

        return " Добавлено оповещение о скидках и подключено к системе, так же добавлен триггер";
    }

    /**
     * Добавление в развернутую систему сущности скидок с правами
     *
     * @return string
     */
    public function addDiscountsEntity()
    {
        Page::insert([
            [
                'name' => 'Скидки',
                'site_id' => 1,
                'title' => 'Скидки',
                'description' => 'Скидки',
                'alias' => 'discounts',
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
            ],
        ]);
        echo "Добавлена страница скидок<br><br>";

        $pages = Page::get();
        $menus = Menu::get();

        Menu::insert([
            [
                'name' => 'Скидки',
                'icon' => null,
                'alias' => 'admin/discounts',
                'tag' => 'discounts',
                'parent_id' => $menus->firstWhere('tag', 'marketings')->id,
                'page_id' => $pages->firstWhere('alias', 'discounts')->id,
                'navigation_id' => 1,
                'company_id' => null,
                'system' => true,
                'author_id' => 1,
                'display' => true,
                'sort' => 5,
            ],
        ]);
        echo "Добавлены пункты меню для скидок<br><br>";

        $entities = Entity::get([
            'id',
            'alias',
        ]);
        Entity::insert([
            [
                'name' => 'Скидки',
                'alias' => 'discounts',
                'model' => 'Discount',
                'rights' => true,
                'system' => true,
                'author_id' => 1,
                'site' => 0,
                'metric' => 0,
                'view_path' => 'system.pages.discounts',
                'page_id' => $pages->firstWhere('alias', 'discounts')->id,
            ],

        ]);
        echo 'Добавлена сущность скидок<br><br>';

        // Наваливание прав
        $entities = Entity::where('alias', 'discounts')
            ->get();

        foreach ($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach ($actions as $action) {
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach ($actionentities as $actionentity) {

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach ($rights as $right) {
                $mass[] = [
                    'right_id' => $right->id,
                    'role_id' => 1,
                    'system' => 1
                ];
            };

            DB::table('right_role')->insert($mass);
        }

        echo "Добавлены права на сущность скидок<br><br>";

        return "<strong>Добавление сущности скидок завершено</strong>";
    }

    /**
     * Обновление 21.04.20
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

        Widget::where('tag', 'sales-department-burden')->update([
            'tag' => 'sales_department_burden'
        ]);
        echo "Обновлен тег виджета нагружки отдела продаж<br><br>";

        $estimatesEntity = Entity::where('alias', 'estimates')
            ->first();
        $estimatesEntity->update([
            'name' => 'Клиентские заказы'
        ]);
        echo "Обновлено имя сущности [Сметы] в [Клиентские заказы]<br><br>";

        $pricesGoodsEntity = Entity::where('alias', 'prices_goods')
            ->first();
        $pricesGoodsEntity->update([
            'name' => 'Позиции товара в прайсе'
        ]);
        echo "Обновлено имя сущности [Прайсы товаров] в [Позиции товара в прайсе]<br><br>";

        Widget::insert([
            'name' => 'Показатели клиентской базы',
            'description' => 'Показатели клиентской базы',
            'tag' => 'clients_indicators',
        ]);
        echo "Добавлен виджет показателей клиентской базы<br><br>";

        set_time_limit(0);

        $estimatesGoodsItems = EstimatesGoodsItem::whereNull('total')
            ->get();
        foreach ($estimatesGoodsItems as $estimatesGoodsItem) {
            if ($estimatesGoodsItem->discount_percent) {
                $estimatesGoodsItem->discount_currency = ($estimatesGoodsItem->amount * $estimatesGoodsItem->discount_percent / 100);
                $estimatesGoodsItem->total = $estimatesGoodsItem->amount - $estimatesGoodsItem->discount_currency;
            } else {
                $estimatesGoodsItem->discount_percent = 0;
                $estimatesGoodsItem->discount_currency = 0;
                $estimatesGoodsItem->total = $estimatesGoodsItem->amount;
            }
            $estimatesGoodsItem->save();
        }
        echo "В пунктах сметы проставлены скидки и тотал<br><br>";

        return "<strong>Обновление 21.04.20 завершено</strong>";
    }

    /**
     * Обновление 13.04.20
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

        foreach ($users as $user) {
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

        foreach ($entities as $entity) {
            // Генерируем права
            $actions = Action::get();
            $mass = [];

            foreach ($actions as $action) {
                $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
            };
            DB::table('action_entity')->insert($mass);

            $actionentities = ActionEntity::where('entity_id', $entity->id)->get();
            $mass = [];

            foreach ($actionentities as $actionentity) {

                $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

                $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
            };

            DB::table('rights')->insert($mass);

            $actionentities = $actionentities->pluck('id')->toArray();

            // Получаем все существующие разрешения (allow)
            $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

            $mass = [];
            // Генерируем права на полный доступ
            foreach ($rights as $right) {
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
