<?php

use Illuminate\Database\Seeder;

use App\LeftoverOperation;

class LeftoverOperationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LeftoverOperation::insert([
        	[
        		'name' => 'Списать'
        	],
        	[
        		'name' => 'Вернуть на склад'
        	],
        	[
        		'name' => 'Создать новый товар'
        	],
        ]);
    }
}
