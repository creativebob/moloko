<?php

use Illuminate\Database\Seeder;

class DatabaseTestSeeder extends Seeder
{

    public function run()
    {

        // Сиды к новым миграциям
        $this->call(LocationsTestTableSeeder::class);
        $this->call(CompaniesTestTableSeeder::class);
        $this->call(SchedulesTestTableSeeder::class);
        $this->call(WorktimesTestTableSeeder::class);
        $this->call(DepartmentsTestTableSeeder::class);
        $this->call(PositionsTestTableSeeder::class);
        $this->call(UsersTestTableSeeder::class);
        $this->call(StaffTestTableSeeder::class);
        $this->call(EmployeesTestTableSeeder::class);

        $this->call(SitesTestTableSeeder::class);
        $this->call(MenuSiteTestTableSeeder::class);
        $this->call(PagesTestTableSeeder::class);
        $this->call(NewsTestTableSeeder::class);
        $this->call(NavigationsTestTableSeeder::class);
        $this->call(MenusTestTableSeeder::class);

    }
}
