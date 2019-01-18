<?php

use Illuminate\Database\Seeder;

class NavigationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('navigations')->insert([
            [
                'name' => 'Левый сайдбар',
                'alias' => 'left-sidebar',
                'align_id' => 2,
                'site_id' => 1,
                'system_item' => 1,
                'display' => 1,
            ],


        ]);
    }
}
