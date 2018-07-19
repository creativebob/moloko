<?php

use Illuminate\Database\Seeder;

class DatabaseExtraSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    // Сиды к новым миграциям
    $this->call(ServicesTypesTableSeeder::class);
    

  }
}
