<?php

namespace App\Providers\System\View;

use App\Http\View\Composers\System\AccessFilialsComposer;
use App\Http\View\Composers\System\AccountsComposer;
use App\Http\View\Composers\System\ArticlesCategoriesWithGroupsComposer;
use App\Http\View\Composers\System\ArticlesCategoriesWithItemsComposer;
use App\Http\View\Composers\System\ArticlesCategoriesWithItemsComposerForManufacturer;
use App\Http\View\Composers\System\AttachmentsComposer;
use App\Http\View\Composers\System\AuthorsComposer;
use App\Http\View\Composers\System\CatalogGoodsWithPricesComposer;
use App\Http\View\Composers\System\CatalogServicesWithPricesComposer;
use App\Http\View\Composers\System\CatalogsGoodsItemsTreeComposer;
use App\Http\View\Composers\System\CatalogsGoodsWithFilialsComposer;
use App\Http\View\Composers\System\CatalogsGoodsWithSchemesComposer;
use App\Http\View\Composers\System\CatalogsServicesItemsTreeComposer;
use App\Http\View\Composers\System\CatalogsServicesWithFilialsComposer;
use App\Http\View\Composers\System\CatalogsServicesWithSchemesComposer;
use App\Http\View\Composers\System\ChannelsComposer;
use App\Http\View\Composers\System\ChargesComposer;
use App\Http\View\Composers\System\CitiesComposer;
use App\Http\View\Composers\System\CitiesWithAreaRegionCountryComposer;
use App\Http\View\Composers\System\CitySearchComposer;
use App\Http\View\Composers\System\ClientsCitiesComposer;
use App\Http\View\Composers\System\ClientsCountComposer;
use App\Http\View\Composers\System\ClientsForSearchComposer;
use App\Http\View\Composers\System\DefaultPhotoSettingsComposer;
use App\Http\View\Composers\System\DepartmentsForUserComposer;
use App\Http\View\Composers\System\DiscountsForEstimatesComposer;
use App\Http\View\Composers\System\EntitiesComposer;
use App\Http\View\Composers\System\EstimatesTotalsComposer;
use App\Http\View\Composers\System\FilialCatalogsGoodsComposer;
use App\Http\View\Composers\System\FilialCatalogsServicesComposer;
use App\Http\View\Composers\System\FilialStaffComposer;
use App\Http\View\Composers\System\Filters\EmploymentHistoryComposer;
use App\Http\View\Composers\System\ImpactsCategoriesWithImpactsComposer;
use App\Http\View\Composers\System\LeadHistoryComposer;
use App\Http\View\Composers\System\MailingListsComposer;
use App\Http\View\Composers\System\MailingsComposer;
use App\Http\View\Composers\System\OutletsSettingsCategoriesWithSettingsComposer;
use App\Http\View\Composers\System\PartsComposer;
use App\Http\View\Composers\System\PaymentsMethodsComposer;
use App\Http\View\Composers\System\PositionsWithStaffComposer;
use App\Http\View\Composers\System\ServicesCategoriesTreeComposer;
use App\Http\View\Composers\System\ServicesCategoriesWithServicesComposer;
use App\Http\View\Composers\System\StaffArchiveCountComposer;
use App\Http\View\Composers\System\SuppliersComposer;
use App\Http\View\Composers\System\TaxationTypesComposer;
use App\Http\View\Composers\System\TemplatesComposer;
use App\Http\View\Composers\System\ToolsTypesComposer;
use App\Http\View\Composers\System\ToolsWithTypeComposer;
use App\Http\View\Composers\System\UsersWithClientComposer;
use App\Http\View\Composers\System\CmvArchivesCountComposer;
use App\Http\View\Composers\System\CompaniesWithClientComposer;
use App\Http\View\Composers\System\ContainersCategoriesComposer;
use App\Http\View\Composers\System\ContainersComposer;
use App\Http\View\Composers\System\CurrenciesComposer;
use App\Http\View\Composers\System\DirectiveCategoriesComposer;
use App\Http\View\Composers\System\DiscountsComposer;
use App\Http\View\Composers\System\DisplayModesComposer;
use App\Http\View\Composers\System\DomainsForFilialComposer;
use App\Http\View\Composers\System\EmployeesActiveCountComposer;
use App\Http\View\Composers\System\EmployeesDismissalCountComposer;
use App\Http\View\Composers\System\EntitiesForDiscountsComposer;
use App\Http\View\Composers\System\Filters\GoodsComposer;
use App\Http\View\Composers\System\Filters\VacanciesComposer;
use App\Http\View\Composers\System\FiltersComposer;
use App\Http\View\Composers\System\GoodsCategoriesTreeComposer;
use App\Http\View\Composers\System\LeadMethodsComposer;
use App\Http\View\Composers\System\LeadTypesComposer;
use App\Http\View\Composers\System\LegalFormsComposer;
use App\Http\View\Composers\System\AgentTypesComposer;
use App\Http\View\Composers\System\ManagersComposer;
use App\Http\View\Composers\System\NotificationsComposer;
use App\Http\View\Composers\System\PaymentsTypesComposer;
use App\Http\View\Composers\System\ProcessesCategoriesWithGroupsComposer;
use App\Http\View\Composers\System\RelatedComposer;
use App\Http\View\Composers\System\CompaniesSettingsCategoriesWithSettingsComposer;
use App\Http\View\Composers\System\SitesWIthFilialsAndCatalogsComposer;
use App\Http\View\Composers\System\SourcesComposer;
use App\Http\View\Composers\System\StagesComposer;
use App\Http\View\Composers\System\StocksComposer;
use App\Http\View\Composers\System\WidgetsComposer;
use App\Http\View\Composers\System\WorkflowsCategoriesWithWorkflowsComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Http\View\Composers\System\SidebarComposer;
use App\Http\View\Composers\System\SectorsSelectComposer;

use App\Http\View\Composers\System\StagesListComposer;

use App\Http\View\Composers\System\CountriesComposer;

use App\Http\View\Composers\System\FilialsForUserComposer;
use App\Http\View\Composers\System\DepartmentsListForUserComposer;

use App\Http\View\Composers\System\UserFilialsComposer;
use App\Http\View\Composers\System\CatalogsServicesItemsForFilialComposer;

use App\Http\View\Composers\System\RolesComposer;

use App\Http\View\Composers\System\LegalFormsSelectComposer;
use App\Http\View\Composers\System\CheckerComposer;

use App\Http\View\Composers\System\LoyaltiesComposer;

use App\Http\View\Composers\System\UnitsCategoriesComposer;
use App\Http\View\Composers\System\UnitsComposer;
use App\Http\View\Composers\System\UnitsArticleComposer;
use App\Http\View\Composers\System\UnitsProcessesComposer;

use App\Http\View\Composers\System\SourceWithSourceServicesComposer;
use App\Http\View\Composers\System\SourceServicesComposer;
use App\Http\View\Composers\System\PeriodsComposer;
use App\Http\View\Composers\System\BooklistTypesComposer;

use App\Http\View\Composers\System\ManufacturersComposer;

use App\Http\View\Composers\System\SupplierSelectComposer;
use App\Http\View\Composers\System\ContragentsComposer;

use App\Http\View\Composers\System\CategoriesSelectComposer;
use App\Http\View\Composers\System\GoodsModesComposer;
use App\Http\View\Composers\System\RawsModesComposer;

use App\Http\View\Composers\System\CatalogsSelectComposer;

use App\Http\View\Composers\System\AccordionsComposer;
use App\Http\View\Composers\System\MenuViewComposer;
use App\Http\View\Composers\System\DepartmentsComposer;
use App\Http\View\Composers\System\DepartmentsViewComposer;
use App\Http\View\Composers\System\FilialsComposer;
// use App\Http\View\Composers\System\SectorsComposer;

use App\Http\View\Composers\System\CategoriesComposer;

use App\Http\View\Composers\System\GoodsCategoriesComposer;
use App\Http\View\Composers\System\RawsCategoriesComposer;
use App\Http\View\Composers\System\GoodsProductsComposer;
use App\Http\View\Composers\System\RawsProductsComposer;
use App\Http\View\Composers\System\AlbumsCategoriesSelectComposer;
use App\Http\View\Composers\System\AlbumsComposer;

use App\Http\View\Composers\System\AlignsComposer;
use App\Http\View\Composers\System\NavigationsCategoriesSelectComposer;
use App\Http\View\Composers\System\MenusSelectComposer;

use App\Http\View\Composers\System\IndicatorsCategoriesSelectComposer;
use App\Http\View\Composers\System\DirectionsComposer;

use App\Http\View\Composers\System\UsersComposer;
use App\Http\View\Composers\System\StaffComposer;
use App\Http\View\Composers\System\EmptyStaffComposer;
use App\Http\View\Composers\System\PositionsComposer;
use App\Http\View\Composers\System\PropertiesComposer;

use App\Http\View\Composers\System\SitesComposer;
use App\Http\View\Composers\System\SiteMenusComposer;
use App\Http\View\Composers\System\PagesComposer;

use App\Http\View\Composers\System\CategoriesDrilldownComposer;

use App\Http\View\Composers\System\EntitiesStatisticsSelectComposer;

use App\Http\View\Composers\System\CatalogsServicesComposer;
use App\Http\View\Composers\System\CatalogsServicesItemsComposer;
use App\Http\View\Composers\System\FilialsForCatalogsServicesComposer;

use App\Http\View\Composers\System\CatalogsGoodsComposer;
use App\Http\View\Composers\System\CatalogsGoodsItemsSelectComposer;
use App\Http\View\Composers\System\FilialsForCatalogsGoodsComposer;

use App\Http\View\Composers\System\CatalogsTypesComposer;

use App\Http\View\Composers\System\ArticlesGroupsComposer;
use App\Http\View\Composers\System\ProcessesGroupsComposer;

use App\Http\View\Composers\System\RawsComposer;
use App\Http\View\Composers\System\GoodsCategoriesWithGoodsComposer;
use App\Http\View\Composers\System\TmcComposer;

use App\Http\View\Composers\System\WorkflowsComposer;
use App\Http\View\Composers\System\ServicesComposer;

use App\Http\View\Composers\System\LeftoverOperationsComposer;

use App\Http\View\Composers\System\ProcessesTypesComposer;

use App\Http\View\Composers\System\RoomsComposer;

use App\Http\View\Composers\System\RubricatorsComposer;
use App\Http\View\Composers\System\RubricatorsItemsComposer;


use App\Http\View\Composers\System\ListChallengesComposer;
use App\Http\View\Composers\System\LeadMethodsListComposer;

class ViewServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // Проверки If Else на шаблонах

        // Display
        Blade::if('display', function ($item) {
            $result = $item->display == 1;
            return $result;
        });

        // Moderation
        Blade::if('moderation', function ($item) {
            $result = $item->moderation == 1;
            return $result;
        });

        // Шаблон
        Blade::if('template', function ($item) {
            $result = is_null($item->company_id) && is_null($item->system);
            return $result;
        });

        view()->composer('layouts.sidebar', SidebarComposer::class);
        view()->composer('includes.selects.sectors_select', SectorsSelectComposer::class);

        view()->composer('includes.selects.countries', CountriesComposer::class);
        view()->composer('includes.selects.stages', StagesListComposer::class);

        view()->composer([
            'includes.selects.filials_for_user',
            'includes.selects.user_filials',
        ], FilialsForUserComposer::class);

        view()->composer('includes.selects.departments_for_user', DepartmentsListForUserComposer::class);
        view()->composer('system.pages.hr.employees.includes.access.roles', DepartmentsForUserComposer::class);

        view()->composer([
            'system.pages.catalogs.goods.prices_goods.select_user_filials',
            'system.pages.catalogs.services.prices_services.select_user_filials',
        ], UserFilialsComposer::class);
        view()->composer('prices_services.sync.modal', CatalogsServicesItemsForFilialComposer::class);

        view()->composer([
            'includes.selects.roles',
            'includes.lists.roles',
            'system.pages.hr.employees.includes.access.roles'
        ], RolesComposer::class);

        view()->composer('includes.lists.charges', ChargesComposer::class);
        view()->composer('includes.lists.widgets', WidgetsComposer::class);
        view()->composer('includes.lists.notifications', NotificationsComposer::class);

        view()->composer('includes.lists.tools_with_type', ToolsWithTypeComposer::class);

        view()->composer('includes.selects.legal_forms', LegalFormsSelectComposer::class);
        view()->composer('includes.selects.agent_types', AgentTypesComposer::class);
        view()->composer('includes.inputs.checker', CheckerComposer::class);
        view()->composer('system.pages.sales.agents.form', CatalogsGoodsWithSchemesComposer::class);
        view()->composer('system.pages.sales.agents.form', CatalogsServicesWithSchemesComposer::class);

        view()->composer('includes.selects.loyalties', LoyaltiesComposer::class);

        view()->composer([
            'system.common.includes.city_search',
            'system.pages.companies.includes.director',
            'leads.personal',
        ], CitySearchComposer::class);

        view()->composer([
            'system.common.includes.city_search',
            'includes.lists.cities',
            'system.pages.companies.includes.director',
            'leads.personal'
        ], CitiesWithAreaRegionCountryComposer::class);

        view()->composer([
            'includes.inputs.checker_contragents',
            'includes.selects.contragents'
        ], ContragentsComposer::class);

        view()->composer(['includes.selects.units_categories'], UnitsCategoriesComposer::class);
        view()->composer([
            'includes.selects.units',
            'includes.selects.units_extra',
        ], UnitsComposer::class);

        view()->composer('products.articles.common.edit.select_units', UnitsArticleComposer::class);
        view()->composer('products.processes.common.edit.select_units', UnitsProcessesComposer::class);

        view()->composer('products.articles.common.edit.edit', ArticlesCategoriesWithGroupsComposer::class);
        view()->composer('products.processes.common.edit.edit', ProcessesCategoriesWithGroupsComposer::class);

        view()->composer('system.pages.documents.consignments.edit', ArticlesCategoriesWithItemsComposer::class);
        view()->composer('system.pages.documents.productions.edit', ArticlesCategoriesWithItemsComposerForManufacturer::class);

        view()->composer(['includes.selects.source_with_source_services'], SourceWithSourceServicesComposer::class);
        view()->composer(['includes.selects.source_services'], SourceServicesComposer::class);

        view()->composer(['includes.selects.periods'], PeriodsComposer::class);

        view()->composer(['includes.selects.booklist_types'], BooklistTypesComposer::class);

        view()->composer([
            'includes.selects.manufacturers',
            'includes.selects.manufacturers_with_placeholder',
            'includes.lists.manufacturers',
            'products.articles.common.edit.manufacturers',
            'system.pages.erp.suppliers.form',
            'system.common.stocks.includes.filters',
        ], ManufacturersComposer::class);

        view()->composer('includes.selects.suppliers', SupplierSelectComposer::class);
        view()->composer([
            'includes.selects.stocks',
//            'leads.tabs.estimate'
        ], StocksComposer::class);

        view()->composer('leads.tabs.payments', PaymentsTypesComposer::class);

        // Conflict: то, что осталось в нижней части
        // view()->composer(['includes.selects.manufacturers', 'includes.lists.manufacturers'], ManufacturersComposer::class);

        view()->composer('includes.selects.goods_modes', GoodsModesComposer::class);
        view()->composer('includes.selects.raws_modes', RawsModesComposer::class);

        view()->composer('includes.selects.users', UsersComposer::class);
        view()->composer('includes.selects.staff', StaffComposer::class);
        view()->composer('includes.selects.empty_staff', EmptyStaffComposer::class);

        view()->composer('includes.selects.positions', PositionsComposer::class);

        view()->composer('includes.lists.positions_with_actual_staff', PositionsWithStaffComposer::class);
        view()->composer([
            'products.common.metrics.properties_list',
            'products.common.metrics.page'
        ], PropertiesComposer::class);

        view()->composer([
            'includes.selects.catalogs_chosen',
            'includes.selects.catalogs'
        ], CatalogsSelectComposer::class);

        view()->composer([
            'includes.lists.sites',
            'includes.selects.sites',
            'system.pages.marketings.users.tabs.general',
            'system.pages.marketings.subscribers.index',
        ], SitesComposer::class);
        view()->composer('includes.lists.site_menus', SiteMenusComposer::class);

        view()->composer('system.pages.domains.plugins', AccountsComposer::class);


        // Страницы сайта
        view()->composer('includes.selects.pages', PagesComposer::class);


        // Select'ы категорий
        view()->composer('includes.selects.categories_select', CategoriesSelectComposer::class);

        view()->composer('includes.drilldowns.categories', CategoriesDrilldownComposer::class);


        // Стандартные шаблоны типа "меню"
        view()->composer('system.common.categories.index.categories_list', AccordionsComposer::class);
        view()->composer('includes.menu_views.category_list', MenuViewComposer::class);
        view()->composer('system.pages.hr.departments.filials_list', DepartmentsViewComposer::class);
        view()->composer('includes.lists.departments', DepartmentsComposer::class);

        // Филиалы
        view()->composer('system.pages.hr.departments.filial.form', CitiesWithAreaRegionCountryComposer::class);

        view()->composer([
            'includes.lists.filials',
            'menus.form'
        ], FilialsComposer::class);

         view()->composer('system.pages.marketings.promotions.form', SitesWIthFilialsAndCatalogsComposer::class);

        view()->composer([
            'includes.selects.categories',
            'products.articles.common.create.categories_select',
            'products.processes.common.create.categories_select'
        ], CategoriesComposer::class);

        view()->composer([
            'includes.selects.goods_categories',
        ], GoodsCategoriesComposer::class);


        view()->composer('includes.selects.raws_categories', RawsCategoriesComposer::class);
        view()->composer('includes.selects.containers_categories', ContainersCategoriesComposer::class);
        view()->composer('includes.selects.goods_products', GoodsProductsComposer::class);
        view()->composer('includes.selects.raws_products', RawsProductsComposer::class);
        view()->composer([
            'includes.selects.albums_categories',
            'system.pages.marketings.albums.select_albums_categories',
            'system.pages.marketings.news.albums.modal_albums',
        ] , AlbumsCategoriesSelectComposer::class);
        view()->composer([
            'includes.selects.albums',
            'system.pages.marketings.news.albums.select_albums',
        ], AlbumsComposer::class);

        view()->composer('includes.selects.aligns', AlignsComposer::class);
        view()->composer('includes.selects.navigations_categories', NavigationsCategoriesSelectComposer::class);
        view()->composer('includes.selects.menus', MenusSelectComposer::class);

        view()->composer('includes.selects.indicators_categories', IndicatorsCategoriesSelectComposer::class);
        view()->composer('includes.selects.directions', DirectionsComposer::class);

        view()->composer('includes.selects.entities_statistics', EntitiesStatisticsSelectComposer::class);
        view()->composer('includes.selects.entities', EntitiesComposer::class);

        view()->composer('system.pages.settings.photo_settings.tabs.settings', DefaultPhotoSettingsComposer::class);

        view()->composer('products.processes.services.prices.catalogs', CatalogsServicesComposer::class);
        view()->composer('products.processes.services.prices.catalogs_items', CatalogsServicesItemsComposer::class);
        view()->composer('products.processes.services.prices.filials', FilialsForCatalogsServicesComposer::class);

        view()->composer([
            'products.articles.goods.prices.catalogs',
//            'leads.catalogs.modal_catalogs_goods'
        ], CatalogsGoodsComposer::class);
        view()->composer('products.articles.goods.prices.prices', CatalogsGoodsWithFilialsComposer::class);

        view()->composer([
            'products.processes.services.prices.catalogs',
//            'leads.catalogs.modal_catalogs_goods'
        ], CatalogsGoodsComposer::class);
        view()->composer('products.processes.services.prices.prices', CatalogsServicesWithFilialsComposer::class);

        view()->composer('products.articles.goods.prices.catalogs_items', CatalogsGoodsItemsSelectComposer::class);
        view()->composer('products.articles.goods.prices.filials', FilialsForCatalogsGoodsComposer::class);

        view()->composer('system.pages.outlets.tabs.catalogs', FilialCatalogsGoodsComposer::class);
        view()->composer('system.pages.outlets.tabs.catalogs', FilialCatalogsServicesComposer::class);

        view()->composer('system.pages.outlets.tabs.staff', FilialStaffComposer::class);

        view()->composer('system.pages.outlets.tabs.settings', PaymentsMethodsComposer::class);

//        view()->composer('leads.tabs.catalogs_goods', CatalogGoodsWithPricesComposer::class);
//        view()->composer('leads.tabs.catalogs_services', CatalogServicesWithPricesComposer::class);

        view()->composer('includes.selects.articles_groups', ArticlesGroupsComposer::class);
        view()->composer('includes.selects.processes_groups', ProcessesGroupsComposer::class);

        view()->composer([
            'products.articles_categories.goods_categories.raws.raws',
            'products.articles.goods.raws.raws'
        ], RawsComposer::class);

        view()->composer([
            'products.articles.goods.containers.containers'
        ], ContainersComposer::class);

        view()->composer([
            'products.articles.goods.attachments.attachments'
        ], AttachmentsComposer::class);

        view()->composer([
            'products.articles.goods.goods.goods',
        ], GoodsCategoriesWithGoodsComposer::class);

        view()->composer([
            'products.articles_categories.goods_categories.related.related',
            'products.articles.goods.related.related'
        ], RelatedComposer::class);

        view()->composer([
            'products.articles.common.edit.tabs.parts'
        ], PartsComposer::class);

        view()->composer([
//            'products.articles_categories.goods_categories.related.related',
            'products.processes.services.impacts.impacts'
        ], ImpactsCategoriesWithImpactsComposer::class);

        view()->composer([
            'products.processes_categories.services_categories.workflows.workflows',
            'products.processes.workflows.workflows.workflows',
            'products.processes.services.workflows.workflows'
        ], WorkflowsCategoriesWithWorkflowsComposer::class);

        view()->composer([
            'products.processes.services.services.services'
        ], ServicesCategoriesWithServicesComposer::class);

        view()->composer('includes.selects.tmc', TmcComposer::class);
        view()->composer([
            'products.articles.goods.raws.leftover_operations_select',
            'products.articles.goods.containers.leftover_operations_select',
        ], LeftoverOperationsComposer::class);

        view()->composer('includes.selects.processes_types', ProcessesTypesComposer::class);

        view()->composer('includes.selects.rooms', RoomsComposer::class);

        view()->composer('includes.selects.mailings', MailingsComposer::class);
        view()->composer('includes.selects.templates', TemplatesComposer::class);
        view()->composer('includes.selects.mailing_lists', MailingListsComposer::class);

        view()->composer('system.pages.marketings.news.rubricators.rubricators', RubricatorsComposer::class);
        view()->composer('system.pages.marketings.news.rubricators.select_rubricators_items', RubricatorsItemsComposer::class);

        view()->composer('layouts.challenges_for_me', ListChallengesComposer::class);

        view()->composer('includes.selects.lead_methods', LeadMethodsListComposer::class);

        view()->composer('includes.selects.channels', ChannelsComposer::class);
        view()->composer([
            'includes.selects.taxation_types',
            'includes.lists.taxation_types',
        ], TaxationTypesComposer::class);

        view()->composer('includes.selects.display_modes', DisplayModesComposer::class);
        view()->composer('includes.selects.directive_categories', DirectiveCategoriesComposer::class);

        view()->composer('includes.lists.filters', FiltersComposer::class);

        view()->composer('includes.lists.currencies', CurrenciesComposer::class);

        view()->composer('system.pages.companies.tabs.settings', CompaniesSettingsCategoriesWithSettingsComposer::class);
        view()->composer('system.pages.outlets.tabs.settings', OutletsSettingsCategoriesWithSettingsComposer::class);

        // Лиды
        view()->composer('leads.personal', ClientsForSearchComposer::class);
        view()->composer('leads.personal', LegalFormsComposer::class);
        view()->composer('leads.personal', MailingsComposer::class);

        view()->composer('leads.tabs.events', StagesComposer::class);
        view()->composer('leads.tabs.history', LeadHistoryComposer::class);
        view()->composer('leads.tabs.estimate', DiscountsForEstimatesComposer::class);

        // Штат
        view()->composer('system.pages.hr.staff.includes.title', StaffArchiveCountComposer::class);
        view()->composer('system.pages.hr.staff.includes.title_dismissal', EmployeesActiveCountComposer::class);

        // Сотрудники
        view()->composer('system.pages.hr.employees.includes.title_active', EmployeesDismissalCountComposer::class);
        view()->composer('system.pages.hr.employees.includes.title_dismissal', EmployeesActiveCountComposer::class);
        view()->composer('system.pages.hr.employees.form', VacanciesComposer::class);
        view()->composer('system.pages.hr.employees.form', EmploymentHistoryComposer::class);

        // Выполненные работы
        view()->composer('system.pages.outcomes.includes.title', ClientsCountComposer::class);

        // Скидки
        view()->composer('system.common.discounts.discounts', DiscountsComposer::class);
        view()->composer('system.pages.marketings.discounts.tabs.general', EntitiesForDiscountsComposer::class);

        view()->composer('system.prints.check_order', DomainsForFilialComposer::class);


        // Фильтры

        view()->composer([
            'system.common.listers.goods',
        ], GoodsComposer::class);

        // Клиенты
        view()->composer('system.pages.clients.includes.filters', SourcesComposer::class);
        view()->composer('system.pages.clients.includes.filters', ClientsCitiesComposer::class);

        // ТМЦ
        view()->composer('products.articles.common.index.includes.title', CmvArchivesCountComposer::class);

        // Товары
        view()->composer('products.articles.goods.includes.filters', GoodsCategoriesTreeComposer::class);

        // Услуги
        view()->composer('products.processes.services.includes.filters', ServicesCategoriesTreeComposer::class);

        view()->composer([
            'products.articles.goods.includes.filters',
            'products.processes.services.includes.filters',
            ], AuthorsComposer::class);

        // Прайсы товаров
        view()->composer('system.pages.catalogs.goods.prices_goods.includes.filters', CatalogsGoodsItemsTreeComposer::class);

        // Прайсы услуг
        view()->composer('system.pages.catalogs.services.prices_services.includes.filters', CatalogsServicesItemsTreeComposer::class);

        // Лиды
        view()->composer('leads.includes.filters', CitiesComposer::class);
        view()->composer('leads.includes.filters', StagesComposer::class);
        view()->composer('leads.includes.filters', ManagersComposer::class);
        view()->composer('leads.includes.filters', LeadMethodsComposer::class);
        view()->composer('leads.includes.filters', LeadTypesComposer::class);
        view()->composer('leads.includes.filters', SourcesComposer::class);

        // Клиентские заказы
        view()->composer('estimates.includes.filters', CitiesComposer::class);
        view()->composer('estimates.includes.filters', SourcesComposer::class);


        view()->composer([
            'estimates.includes.filters',
            'system.pages.clients.includes.filters',
        ], AccessFilialsComposer::class);

        // Рассылки
        view()->composer('system.pages.marketings.subscribers.index', MailingListsComposer::class);

        // Товарные накладные
        view()->composer('system.pages.documents.consignments.includes.filters', SuppliersComposer::class);


        view()->composer('estimates.includes.totals', EstimatesTotalsComposer::class);


        view()->composer('includes.selects.tools_types', ToolsTypesComposer::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
