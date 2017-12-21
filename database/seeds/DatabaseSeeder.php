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


            // Пользователи и компании
            $this->call(CompaniesTableSeeder::class);
            $this->call(UsersTableSeeder::class);

            // Наполнение локализаций
            $this->call(RegionsTableSeeder::class);
            $this->call(AreasTableSeeder::class);
            $this->call(CitiesTableSeeder::class);

            // Наполнение таблиц с правами
            $this->call(CategoryRightsTableSeeder::class);
            $this->call(RightsTableSeeder::class);
    		$this->call(RolesTableSeeder::class);
            $this->call(RightRoleTableSeeder::class);
            $this->call(RoleUserTableSeeder::class);

            // Сайты, страницы
            $this->call(SitesTableSeeder::class);
            $this->call(PagesTableSeeder::class);

            // Должности
            $this->call(PositionsTableSeeder::class);

            // Сущности
            $this->call(EntitiesTableSeeder::class);
            
    }
}
