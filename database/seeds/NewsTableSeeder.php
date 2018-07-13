<?php

use Illuminate\Database\Seeder;

class NewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Создаем новости дял сайта
    	DB::table('news')->insert([
    		
    	]);
    }
  }
