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
    // Сферы деятельности компаний и должностей
    $this->call(SectorsTableSeeder::class);

    // Графики работы
    $this->call(SchedulesTableSeeder::class);
    $this->call(WorktimesTableSeeder::class);

     // Наполнение локализаций
    $this->call(CountriesTableSeeder::class);
    $this->call(RegionsTableSeeder::class);
    $this->call(AreasTableSeeder::class);
    $this->call(CitiesTableSeeder::class);

    // Локации
    $this->call(LocationsTableSeeder::class);

    // Пользователи и компании
    $this->call(CompaniesTableSeeder::class);

   

    // Филиалы / отделы
    $this->call(DepartmentsTableSeeder::class);
    $this->call(UsersTableSeeder::class);

    // Сайты, страницы
    $this->call(SitesTableSeeder::class);
    $this->call(PagesTableSeeder::class);

    // Должности
    $this->call(PositionsTableSeeder::class);

    // Сущности
    $this->call(EntitiesTableSeeder::class);

    // Действия над сущностями
    $this->call(ActionsTableSeeder::class);

    // Создаем связи между действиями и сущностями
    $this->call(ActionEntityTableSeeder::class);
    $this->call(EntityPageTableSeeder::class);
    
    // Наполнение таблиц с правами
    $this->call(CategoryRightsTableSeeder::class);
    $this->call(RightsTableSeeder::class);
    $this->call(RolesTableSeeder::class);
    $this->call(RightRoleTableSeeder::class);
    $this->call(RoleUserTableSeeder::class);
    $this->call(PositionRoleTableSeeder::class);

    // Вакансии и сотрудники
    $this->call(StaffTableSeeder::class);
    $this->call(EmployeesTableSeeder::class);

    // Меню
    $this->call(NavigationsCategoriesTableSeeder::class);
    $this->call(NavigationsTableSeeder::class);
    $this->call(MenusTableSeeder::class);
    $this->call(MenuSiteTableSeeder::class);

    // Списки
    $this->call(BooklistsTableSeeder::class);
    $this->call(BooklistUserTableSeeder::class);
    $this->call(ListItemsTableSeeder::class);

    // Папки
    $this->call(FolderTableSeeder::class);

    // Новости
    $this->call(NewsTableSeeder::class);
    $this->call(CityEntityTableSeeder::class);

    // Альбомы
    $this->call(AlbumsCategoriesTableSeeder::class);
    $this->call(AlbumsTableSeeder::class);

    // Единицы измерения
    $this->call(UnitsTableSeeder::class);

    // Товары
    $this->call(ProductsTypesTableSeeder::class);
    $this->call(ProductsCategoriesTableSeeder::class);
    $this->call(ProductsTableSeeder::class);

    // Связь: Расписания с сущностями
    $this->call(ScheduleEntityTableSeeder::class);

    // Связь: Клиенты - поставщики
    $this->call(ContragentsTableSeeder::class);

  }
}
