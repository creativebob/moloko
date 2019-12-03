<?php

use Illuminate\Database\Seeder;
use App\Trigger;

class TriggersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Trigger::insert([
            [
                'name' => 'Лид с сайта',
                'alias' => 'create-lead-from-project',
                'description' => '',
            ],
            [
                'name' => 'Рекламация',
                'alias' => 'create-claim',
                'description' => '',
            ],
            [
                'name' => 'Уведомление',
                'alias' => 'notification',
                'description' => '',
            ],
            [
                'name' => 'Предложение',
                'alias' => 'offer',
                'description' => '',
            ],
        ]);
    }
}
