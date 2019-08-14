<?php

use Illuminate\Database\Seeder;

class ServicesCategoriesTestTableSeeder extends Seeder
{
    
    public function run()
    {
        DB::table('services_categories')->insert([

            // 1
            [
                'company_id' => 1,
                'name' => 'Замеры',
                'description' => '', 
                'parent_id' => null, 
                'category_id' => null, 
                'category_status' => 1,               
                'author_id' => 1, 
                'display' => true,
            ],

            // 2
            [
                'company_id' => 1,
                'name' => 'Проектные работы',
                'description' => '', 
                'parent_id' => null, 
                'category_id' => null,
                'category_status' => 1,    
                'author_id' => 1, 
                'display' => true,
            ],

            // 3
            [
                'company_id' => 1,
                'name' => 'Производство',
                'description' => '', 
                'parent_id' => null, 
                'category_id' => null,
                'category_status' => 1,                 
                'author_id' => 1, 
                'display' => true,
            ],

            // 4
            [
                'company_id' => 1,
                'name' => 'Доставка',
                'description' => '', 
                'parent_id' => null, 
                'category_id' => null,
                'category_status' => 1,                
                'author_id' => 1, 
                'display' => true,
            ],

            // 5
            [
                'company_id' => 1,
                'name' => 'Монтажи',
                'description' => '', 
                'parent_id' => null, 
                'category_id' => null, 
                'category_status' => 1,               
                'author_id' => 1, 
                'display' => true,
            ],

            // 6
            [
                'company_id' => 1,
                'name' => 'Обслуживание',
                'description' => '', 
                'parent_id' => null, 
                'category_id' => null, 
                'category_status' => 1,                
                'author_id' => 1, 
                'display' => true,
            ],

            // 7
            [
                'company_id' => 1,
                'name' => 'Ремонт',
                'description' => '', 
                'parent_id' => null, 
                'category_id' => null,
                'category_status' => 1,                 
                'author_id' => 1, 
                'display' => true,
            ],

            // 8
            [
                'company_id' => 1,
                'name' => 'Консультации',
                'description' => '', 
                'parent_id' => null, 
                'category_id' => null, 
                'category_status' => 1,               
                'author_id' => 1, 
                'display' => true,
            ],

            // 9
            [
                'company_id' => 1,
                'name' => 'Монтаж гаражных ворот',
                'description' => '', 
                'parent_id' => 5, 
                'category_id' => 5, 
                'category_status' => null,                 
                'author_id' => 1, 
                'display' => true,
            ],

            // 10
            [
                'company_id' => 1,
                'name' => 'Монтаж уличных ворот',
                'description' => '', 
                'parent_id' => 5, 
                'category_id' => 5,   
                'category_status' => null,             
                'author_id' => 1, 
                'display' => true,
            ],

            // 11
            [
                'company_id' => 1,
                'name' => 'Монтаж автоматики',
                'description' => '', 
                'parent_id' => 5, 
                'category_id' => 5,    
                'category_status' => null,     
                'author_id' => 1, 
                'display' => true,
            ],

            // 12
            [
                'company_id' => 1,
                'name' => 'Монтаж рольставень',
                'description' => '', 
                'parent_id' => 5, 
                'category_id' => 5,     
                'category_status' => null,      
                'author_id' => 1, 
                'display' => true,
            ],

            // 13
            [
                'company_id' => 1,
                'name' => 'Монтаж алюминиевых конструкций',
                'description' => '', 
                'parent_id' => 5, 
                'category_id' => 5,   
                'category_status' => null,            
                'author_id' => 1, 
                'display' => true,
            ],

            // 14
            [
                'company_id' => 1,
                'name' => 'Производство откатных ворот',
                'description' => '', 
                'parent_id' => 3, 
                'category_id' => 3,   
                'category_status' => null,            
                'author_id' => 1, 
                'display' => true,
            ],

            // 15
            [
                'company_id' => 1,
                'name' => 'Производство алюминиевых конструкций',
                'description' => '', 
                'parent_id' => 3, 
                'category_id' => 3,   
                'category_status' => null,            
                'author_id' => 1, 
                'display' => true,
            ],

        ]);
    }
}
