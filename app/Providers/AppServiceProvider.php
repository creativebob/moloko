<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Menu;
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
        // Меню для левого сайдбара
        // Знаем что статика, поэтому указываем в таблице навигации первый id
        if (Schema::hasTable('menus')) { 
            // Передаем меню на все страницы приложения
            $sidebar = Menu::with('page')->whereNavigation_id(2)->get()->toArray();
            //Создаем масив где ключ массива является ID меню
            $sidebar_id = [];
            foreach ($sidebar as $sidebar_item) {
              $sidebar_id[$sidebar_item['id']] = $sidebar_item;
            };
            //Функция построения дерева из массива от Tommy Lacroix
            $sidebar_tree = [];
            foreach ($sidebar_id as $id => &$node) {   
              //Если нет вложений
              if (!$node['menu_parent_id']){
                $sidebar_tree[$id] = &$node;
              } else { 
              //Если есть потомки то перебераем массив
                $sidebar_id[$node['menu_parent_id']]['children'][$id] = &$node;
              }
            };
            // dd($sidebar_tree);
            View::share('sidebar_tree', $sidebar_tree);
        }
        // Конец меню для левого сайдбара

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
