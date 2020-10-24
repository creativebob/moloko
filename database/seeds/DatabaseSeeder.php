<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        // Локации
        // $this->call(LocationsTableSeeder::class);

        // Пользователи
        $this->call(UsersTableSeeder::class);

        // Наполнение локализаций
        $this->call(CountriesTableSeeder::class);
        $this->call(RegionsTableSeeder::class);
        $this->call(CitiesTableSeeder::class);

        // Единицы измерения
        $this->call(UnitsTableSeeder::class);

        // Сферы деятельности компаний и должностей
        $this->call(SectorsTableSeeder::class);
        $this->call(LegalFormsTableSeeder::class);


        $this->call(BooklistTypesTableSeeder::class);

        // Сайты, страницы
        $this->call(SitesTableSeeder::class);
        $this->call(PagesTableSeeder::class);

        $this->call(AlignsTableSeeder::class);

        $this->call(CurrenciesTableSeeder::class);

        $this->call(DisplayModesTableSeeder::class);

        // Сущности
        $this->call(EntitiesTableSeeder::class);

        // Действия над сущностями
        $this->call(ActionsTableSeeder::class);

        // Создаем связи между действиями и сущностями
        $this->call(ActionEntityTableSeeder::class);

        // Меню
        // $this->call(NavigationsCategoriesTableSeeder::class);
        $this->call(NavigationsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(MenuSiteTableSeeder::class);

        // Должности
//        $this->call(PositionsTableSeeder::class);

        // Наполнение таблиц с правами
        $this->call(CategoryRightsTableSeeder::class);
        $this->call(RightsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(RightRoleTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
//        $this->call(PositionRoleTableSeeder::class);
        $this->call(ChargesTableSeeder::class);

        // Альбомы
        $this->call(PhotoSettingsTableSeeder::class);
        $this->call(AlbumsCategoriesTableSeeder::class);

        // Свойства (для метрик)
        $this->call(PropertiesTableSeeder::class);

        // Сиды к новым миграциям
        $this->call(ProcessesTypesTableSeeder::class);

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

        $this->call(ChannelsTableSeeder::class);
        $this->call(TriggersTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);
        $this->call(NotificationSiteTableSeeder::class);

        $this->call(LeftoverOperationsTableSeeder::class);
        $this->call(PaymentsTypesTableSeeder::class);

        // Справочные сиды: фамилии и имена
        $this->call(FirstNamesTableSeeder::class);
        $this->call(SurnamesTableSeeder::class);

        // Настройки компании
        $this->call([
            CompaniesSettingsCategoriesTableSeeder::class,
            CompaniesSettingsTableSeeder::class
        ]);

//         $this->call(StartTableSeeder::class);


        $this->call(TemplatesCategoriesTableSeeder::class);


    }
}
