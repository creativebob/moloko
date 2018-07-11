<?php
use Illuminate\Database\Seeder;
class ServicesCategoriesTableSeeder extends Seeder
{
  public function run()
  {
    DB::table('services_categories')->insert([
      [
        'company_id' => 1,
        'name' => 'Монтаж',
        'parent_id' => null,
        'category_status' => 1,
        'author_id' => 1,
        'category_id' => null,
        'display' => 1,
        // 'type' => 'services',
        'status' => 'one',
        'services_mode_id' => 2,
      ],
      [
        'company_id' => 1,
        'name' => 'Вложенный',
        'parent_id' => 1,
        'category_status' => null,
        'author_id' => 1,
        'category_id' => 1,
        'display' => 1,
        // 'type' => 'services',
        'status' => 'one',
        'services_mode_id' => 2,
      ],


    ]);
}
}