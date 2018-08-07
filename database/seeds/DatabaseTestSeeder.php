<?php

use Illuminate\Database\Seeder;

class DatabaseTestSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    // Сиды к новым миграциям
    $this->call(CompaniesTestTableSeeder::class);

  }
}
