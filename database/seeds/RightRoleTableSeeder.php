<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Right;

class RightRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run()
    {

        // Получаем все существующие разрешения (allow) 
        $rights = Right::where('directive', 'allow')->get();
        $mass = [];

        // Генерируем права на полный доступ
        foreach($rights as $right){
            $mass[] = ['right_id' => $right->id, 'role_id' => 1, 'system_item' => 1];
        }

        DB::table('right_role')->insert($mass);

        // Генерируем прочее право
        DB::table('right_role')->insert([

        	['right_id' => 1, 'role_id' => 2], 
        	['right_id' => 2, 'role_id' => 2], 
        	['right_id' => 3, 'role_id' => 2], 
        	['right_id' => 4, 'role_id' => 2], 
            ['right_id' => 5, 'role_id' => 2], 
            ['right_id' => 6, 'role_id' => 2], 
            ['right_id' => 7, 'role_id' => 2], 

            ['right_id' => 8, 'role_id' => 3], 
            ['right_id' => 9, 'role_id' => 3], 
            ['right_id' => 10, 'role_id' => 3], 
            ['right_id' => 11, 'role_id' => 3], 
            ['right_id' => 12, 'role_id' => 3], 
            ['right_id' => 13, 'role_id' => 3], 
            ['right_id' => 14, 'role_id' => 3], 

            ['right_id' => 8, 'role_id' => 4], 
            ['right_id' => 9, 'role_id' => 4], 
            ['right_id' => 10, 'role_id' => 4], 
            ['right_id' => 11, 'role_id' => 4], 
            ['right_id' => 12, 'role_id' => 4], 
            ['right_id' => 13, 'role_id' => 4], 
            ['right_id' => 14, 'role_id' => 4], 

            ['right_id' => 8, 'role_id' => 4], 
            ['right_id' => 9, 'role_id' => 4], 
            ['right_id' => 10, 'role_id' => 4], 
            ['right_id' => 11, 'role_id' => 4], 
            ['right_id' => 12, 'role_id' => 4], 
            ['right_id' => 13, 'role_id' => 5], 
            ['right_id' => 14, 'role_id' => 5], 

        ]);
    }
}
