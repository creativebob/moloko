<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Menu;
use App\Site;
use App\EntitySetting;

use Illuminate\Support\Facades\Blade;


use Illuminate\Support\Facades\Auth;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // Если существует таблица с меню
        if (Schema::hasTable('menus')) {

            // Получаем все пункты меню
            // Знаем что статика, поэтому указываем в таблице навигации id, получаем массив
            $sidebar = Menu::with('page', 'page.entities')->where(['navigation_id' => 2, 'display' => 1])->orderBy('sort', 'asc')->get()->toArray();
            // dd($sidebar);

            view()->composer('*', function($view) use ($sidebar) {

                // Получаем список сущностей из сессии
                $session = app('session')->get('access');
                $entities_list = $session['settings']['entities_list'];
                // dd($entities_list);

                if (empty($entities_list)) {
                    $entities_list = [];
                };
                // dd($entities_list);

                // Меню для левого сайдбара
                // Создаем масив где ключ массива является ID меню
                $sidebar_id = [];
                foreach ($sidebar as $sidebar_item) {
                    if ($sidebar_item['page']['entities'] != null) {
                        foreach ($sidebar_item['page']['entities'] as $entity) {
                            if (in_array($entity['id'], $entities_list)) {
                                $sidebar_id[$sidebar_item['id']] = $sidebar_item;
                            }
                        }
                    } else {
                        $sidebar_id[$sidebar_item['id']] = $sidebar_item;
                    }
                };
                // dd($sidebar_id);

                // Функция построения дерева из массива от Tommy Lacroix
                $sidebar_tree = [];
                foreach ($sidebar_id as $id => &$node) {   

                    // Если нет вложений
                    if (!$node['parent_id']){
                        $sidebar_tree[$id] = &$node;
                    } else { 
                        // Если есть потомки то перебераем массив
                        $sidebar_id[$node['parent_id']]['children'][$id] = &$node;
                    }
                }

                $sidebar_final = [];
                foreach ($sidebar_tree as $id => &$node) {
                    if (isset($node['children'])) {
                        $sidebar_final[$id] = &$node;
                    }
                }
                // dd($sidebar_final);

                // Отдаем меню на шаблон
                $view->with('sidebar_tree', $sidebar_final);   
                // View::share('sidebar_tree', $sidebar_tree);
                // Конец меню для левого сайдбара
            });
        }

        // dd(env('SITE_API_TOKEN'));

        // Умолчания на случай, если нет доступа к базе (Для формирования autoload)
        $settings = [];
        $settings['img_small_width'] = 0;
        $settings['img_small_height'] = 0;
        $settings['img_medium_width'] = 0;
        $settings['img_medium_height'] = 0;
        $settings['img_large_width'] = 0;
        $settings['img_large_height'] = 0;   

        $settings['img_formats'] = 0;

        $settings['img_min_width'] = 0;
        $settings['img_min_height'] = 0;   
        $settings['img_max_size'] = 0;


        // Если существует таблица с меню
        if (Schema::hasTable('entity_settings')) {
            $get_settings = EntitySetting::whereNull('company_id')->first();

            // dd($get_settings);

            if($get_settings != null){
                $settings['img_small_width'] = $get_settings->img_small_width;
                $settings['img_small_height'] = $get_settings->img_small_height;
                $settings['img_medium_width'] = $get_settings->img_medium_width;
                $settings['img_medium_height'] = $get_settings->img_medium_height;
                $settings['img_large_width'] = $get_settings->img_large_width;
                $settings['img_large_height'] = $get_settings->img_large_height;   

                $settings['img_formats'] = $get_settings->img_formats;

                $settings['img_min_width'] = $get_settings->img_min_width;
                $settings['img_min_height'] = $get_settings->img_min_height;   
                $settings['img_max_size'] = $get_settings->img_max_size;
            };

            config()->set('settings', $settings);

            // View::share(compact('settings'));

          // dd(config()->get('settings'));
        }

        // Проверки If Else на шаблонах

        // Display
        Blade::if('display', function ($item) {
            return $item->display == 1;
        });

        // System item
        // Blade::if('system', function ($item) {
        //     return $item->display == 1;
        // });
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      //
    }
}
