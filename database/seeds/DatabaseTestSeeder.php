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
    $this->call(LocationsTestTableSeeder::class);
    $this->call(CompaniesTestTableSeeder::class);
    $this->call(DepartmentsTestTableSeeder::class);
    $this->call(PositionsTestTableSeeder::class);
    $this->call(UsersTestTableSeeder::class);
    $this->call(StaffTestTableSeeder::class);

    $this->call(SitesTestTableSeeder::class);
    $this->call(PagesTestTableSeeder::class);

  }
}
