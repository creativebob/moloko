<?php

use Illuminate\Database\Seeder;

class ServicesModesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services_modes')->insert([
            [
                'name' => 'Услуга',
                'description' => '',
                'alias' => 'services',
                'type' => 'services',
            ],
            [
                'name' => 'Монтаж',
                'description' => '',
                'alias' => 'installs',
                'type' => 'services',
            ],
            [
                'name' => 'Доставка',
                'description' => '',
                'alias' => 'deliveries',
                'type' => 'services',
            ],
            [
                'name' => 'Замер',
                'description' => '',
                'alias' => 'measurements',
                'type' => 'services',
            ],

    	]);
    }
}
