<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        // Сферы деятельности компаний и должностей
        $this->call(SectorsTableSeeder::class);
        $this->call(LegalFormsTableSeeder::class);

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

        // Единицы измерения
        $this->call(UnitsCategoriesTableSeeder::class);
        $this->call(UnitsTableSeeder::class);

        // Сайты, страницы
        $this->call(SitesTableSeeder::class);
        $this->call(PagesTableSeeder::class);

        $this->call(DepartmentSiteTableSeeder::class);

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
        $this->call(ChargesTableSeeder::class);

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
        $this->call(BooklistTypesTableSeeder::class);
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
        $this->call(EntitySettingsTableSeeder::class);

        // Услуги
        $this->call(ServicesModesTableSeeder::class);
        $this->call(ServicesCategoriesTableSeeder::class);
        $this->call(ServicesProductsTableSeeder::class);
        $this->call(ServicesTableSeeder::class);

        // Связь: Расписания с сущностями
        $this->call(ScheduleEntityTableSeeder::class);

        // Связь: Клиенты - поставщики
        $this->call(ContragentsTableSeeder::class);

        $this->call(PropertiesTableSeeder::class);
        $this->call(MetricsTableSeeder::class);
        $this->call(MetricEntityTableSeeder::class);

        // Сиды к новым миграциям
        $this->call(ServicesTypesTableSeeder::class);
        $this->call(GoodsModesTableSeeder::class);

        $this->call(RawsModesTableSeeder::class);

        // Помещения
        $this->call(PlacesTypesTableSeeder::class);

        // Маркетинг
        $this->call(MediumsTableSeeder::class);
        $this->call(SourcesTableSeeder::class);
        $this->call(SourceServicesTableSeeder::class);
        $this->call(LeadTypesTableSeeder::class);
        $this->call(LeadMethodsTableSeeder::class);

        $this->call(ChallengesTypesTableSeeder::class);
        $this->call(PrioritiesTableSeeder::class);

        // Работа с клиентом
        $this->call(LoyaltiesTableSeeder::class);

        $this->call(StagesTableSeeder::class);
        $this->call(MessengersTableSeeder::class);

        // Настройки должности
        $this->call(WidgetsTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);


    }
}
