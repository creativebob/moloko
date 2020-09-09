<?php

use Illuminate\Database\Seeder;
use App\Trigger;
use App\Entity;

class TriggersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $entities = Entity::get();

        Trigger::insert([
            [
                'name' => 'Лид с сайта',
                'alias' => 'create-lead-from-project',
                'description' => null,
                'entity_id' => $entities->firstWhere('alias', 'leads')->id,
            ],
            [
                'name' => 'Рекламация',
                'alias' => 'create-claim',
                'description' => null,
                'entity_id' => $entities->firstWhere('alias', 'claims')->id,
            ],
            [
                'name' => 'Уведомление',
                'alias' => 'notification',
                'description' => null,
                'entity_id' => null,
            ],
            [
                'name' => 'Предложение',
                'alias' => 'offer',
                'description' => null,
                'entity_id' => null,
            ],
            [
                'name' => 'Контроль вкл / выкл скидок',
                'alias' => 'discounts-recalculate',
                'description' => null,
                'entity_id' => $entities->firstWhere('alias', 'discounts')->id,
            ],
        ]);
    }
}
