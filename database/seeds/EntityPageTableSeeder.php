<?php

use Illuminate\Database\Seeder;

use App\Entity;
use App\Page;

class EntityPageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $entities = Entity::get(['id', 'alias']);
        $pages = Page::where('site_id', 1)->get(['id', 'alias']);

        $mass = [];
        foreach ($entities as $entity) {
            foreach ($pages as $page) {
                if ($entity->alias == $page->alias) {
                    $mass[] = ['entity_id' => $entity->id, 'page_id' => $page->id];
                    // break;
                }
            }
        }

        DB::table('entity_page')->insert($mass);
    }
}
