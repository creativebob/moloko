<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Menu;

use Illuminate\Support\Facades\Auth;

use App\Policies\SitePolicy;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

      if (Schema::hasTable('menus')) {


            // Передаем меню на все страницы приложения
            $sidebar = Menu::with('page', 'page.entities')->whereNavigation_id(2)->get()->toArray();
      }

      view()->composer('*', function($view) use ($sidebar) {
        // dd(app('session')->get('access'));
        $session = [
          1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
        ];
        // dd($session);
      
        // Меню для левого сайдбара
        // Знаем что статика, поэтому указываем в таблице навигации первый id
        

            // dd($sidebar);
          if ($sidebar) {
            // Создаем масив где ключ массива является ID меню
            $sidebar_id = [];
            foreach ($sidebar as $sidebar_item) {
              
              if ($sidebar_item['page']['entities'] != null) {
                foreach ($sidebar_item['page']['entities'] as $entity) {
                  if (in_array($entity['id'], $session)) {
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
              if (!$node['menu_parent_id']){
                $sidebar_tree[$id] = &$node;
              } else { 
              // Если есть потомки то перебераем массив
                $sidebar_id[$node['menu_parent_id']]['children'][$id] = &$node;
              }
            };

            $sidebar_final = [];
            foreach ($sidebar_tree as $id => &$node) {
              if (isset($node['children'])) {
                $sidebar_final[$id] = &$node;
              }
            };
            // dd($sidebar_final);
            $view->with('sidebar_tree', $sidebar_final);   
            // View::share('sidebar_tree', $sidebar_tree);
            // Конец меню для левого сайдбара
          } 
        });
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
