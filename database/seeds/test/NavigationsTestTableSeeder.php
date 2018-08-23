<?php

use Illuminate\Database\Seeder;

class NavigationsTestTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('navigations')->insert([
            [
                'name' => 'Навигация по сайту',
                'site_id' => 2,  
                'system_item' => null, 
                'company_id' => 1,
                'navigations_category_id' => 1, 
                'display' => 1,            
            ],
            [
                'name' => 'Меню продукции',
                'site_id' => 2, 
                'system_item' => null,  
                'company_id' => 1,
                'navigations_category_id' => 2,  
                'display' => 1,           
            ],
            [
                'name' => 'Меню в футере',
                'site_id' => 2, 
                'system_item' => null,  
                'company_id' => 1,
                'navigations_category_id' => 4,  
                'display' => 1,           
            ],
            [
                'name' => 'Соц-медиа',
                'site_id' => 2, 
                'system_item' => null,  
                'company_id' => 1,
                'navigations_category_id' => 3,  
                'display' => 1,           
            ],

        ]);
    }
}
