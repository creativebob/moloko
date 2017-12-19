<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Наполнение локализаций
        $this->call(RegionsTableSeeder::class);
        $this->call(AreasTableSeeder::class);
        $this->call(CitiesTableSeeder::class);

        // Наполнение таблиц с правами
        $this->call(CategotyRightTableSeeder::class);
        $this->call(RightsTableSeeder::class);
		$this->call(AccessGroupsTableSeeder::class);
        $this->call(AccessesTableSeeder::class);

        // Пользователи и компании
        $this->call(CompaniesTableSeeder::class);
        $this->call(UsersTableSeeder::class);

        // Сайты, страницы
        $this->call(SitesTableSeeder::class);
        $this->call(PagesTableSeeder::class);

        // Должности
        $this->call(PositionsTableSeeder::class);
            
    }
}
