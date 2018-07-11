<?php

use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('articles')->insert([
    		[
    			'name' => 'Откатные ворота',

    			'cost' => 30000,
    			'price' => 35000,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Секционные ворота',

    			'cost' => 24000,
    			'price' => null,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Забор',

    			'cost' => null,
    			'price' => null,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Труба',

    			'cost' => 1000,
    			'price' => null,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Прутик',

    			'cost' => 100,
    			'price' => null,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],
    		[
    			'name' => 'Полуфабрик',

    			'cost' => 5000,
    			'price' => 7000,
    			'company_id' => 1, 
    			'author_id' => 4, 
    		],

            // Шторка
            [
                'name' => 'Блекаут ВЕНЗЕЛЬ розовый',

                'cost' => null,
                'price' => 700,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Блекаут ВЕНЗЕЛЬ терракотовый',

                'cost' => null,
                'price' => 700,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Блекаут БАБОЧКИ',

                'cost' => null,
                'price' => 480,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Блекаут ЛИСТЬЯ',

                'cost' => null,
                'price' => 590,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Блекаут INTERIO',

                'cost' => null,
                'price' => 670,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Блекаут Люстра',

                'cost' => null,
                'price' => 750,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард Дебют Цветы',
 
                'cost' => null,
                'price' => 250,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард BYRA LUX',
      
                'cost' => null,
                'price' => 753,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард POINT',
  
                'cost' => null,
                'price' => 900,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард VALENTINA',

                'cost' => null,
                'price' => 1000,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард Диана',
 
                'cost' => null,
                'price' => 320,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард Зебра',
        
                'cost' => null,
                'price' => 390,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард Кудри',
    
                'cost' => null,
                'price' => 560,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард ZARA',
         
                'cost' => null,
                'price' => 6690,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард MOSTAR',
         
                'cost' => null,
                'price' => 340,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард Полоска',
      
                'cost' => null,
                'price' => 460,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард  Шенил',
         
                'cost' => null,
                'price' => 590,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард Анита',
          
                'cost' => null,
                'price' => 460,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард 7700',
         
                'cost' => null,
                'price' => 740,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Жаккард Классика',
          
                'cost' => null,
                'price' => 330,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Тафта Вензель',
       
                'cost' => null,
                'price' => 600,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Тафта Кружева',
    
                'cost' => null,
                'price' => 600,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Канвас однотонный',

                'cost' => null,
                'price' => 750,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Бархат ADELYA',
       
                'cost' => null,
                'price' => 830,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Софт двусторонний',
    
                'cost' => null,
                'price' => 770,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Лен Interio',
     
                'cost' => null,
                'price' => 680,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Лен МИСКЕТ',
  
                'cost' => null,
                'price' => 680,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Лен РОМБ',
     
                'cost' => null,
                'price' => 520,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Лен ЦВЕТЫ',
     
                'cost' => null,
                'price' => 560,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Лен SANPA',
  
                'cost' => null,
                'price' => 560,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Лен с термопечатью',
       
                'cost' => null,
                'price' => 490,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Лен ЛИСТ Ламелла',
    
                'cost' => null,
                'price' => 530,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Лен ПОЛОСКА',
       
                'cost' => null,
                'price' => 580,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Блекаут однотонный Бирюза',
        
                'cost' => null,
                'price' => 620,
                'company_id' => 4, 
                'author_id' => 15, 
            ],
            [
                'name' => 'Блекаут однотонный Болотный',
      
                'cost' => null,
                'price' => 620,
                'company_id' => 4, 
                'author_id' => 15, 
            ],

    	]);
    }
}
