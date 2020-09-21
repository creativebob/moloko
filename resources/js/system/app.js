/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../common/bootstrap');

// window.Vue = require('vue');
// window.Vuex = require('vuex');

import Vue from 'vue';
window.Vue = Vue;

import Vuex from 'vuex';
window.Vuex = Vuex;
Vue.use(Vuex);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Инпуты
Vue.component('input-time-component', require('./components/inputs/TimeComponent'));
Vue.component('input-phone-component', require('./components/inputs/PhoneComponent'));

Vue.component('discount-mode-component', require('./components/discounts/DiscountModeComponent'));

Vue.component('consignment-component', require('./components/consignments/ConsignmentComponent.vue'));
Vue.component('production-component', require('./components/productions/ProductionComponent.vue'));

// Смета на лиде
Vue.component('estimate-component', require('./components/leads/estimates/EstimateComponent.vue'));
Vue.component('payments-component', require('./components/leads/PaymentsComponent.vue'));

Vue.component('checkboxer-component', require('./components/common/CheckboxerComponent'));
Vue.component('lister-component', require('./components/common/ListerComponent'));
Vue.component('categorier-component', require('./components/common/categories/CategorierComponent'));

Vue.component('digit-component', require('./components/inputs/DigitComponent'));
Vue.component('input-digit-component', require('./components/InputDigitComponent.vue'));
Vue.component('dropzone-component', require('./components/DropzoneComponent.vue'));

// ТМЦ
Vue.component('articles-categories-with-groups-component', require('./components/ArticlesCategoriesWithGroupsComponent.vue'));
Vue.component('manufacturers-component', require('./components/ManufacturersComponent.vue'));

Vue.component('goods-store-component', require('./components/products/articles/common/GoodsStateComponent'));
Vue.component('compositions-component', require('./components/products/articles/compositions/CompositionsComponent'));
Vue.component('presets-component', require('./components/products/articles/presets/PresetsComponent.vue'));

// Услуги
Vue.component('processes-categories-with-groups-component', require('./components/ProcessesCategoriesWithGroupsComponent.vue'));

Vue.component('lead-personal-component', require('./components/leads/LeadPersonalComponent'));
Vue.component('catalog-goods-component', require('./components/leads/catalogs/goods/CatalogGoodsComponent.vue'));
Vue.component('catalog-services-component', require('./components/leads/catalogs/services/CatalogServicesComponent.vue'));
Vue.component('lead-badget-component', require('./components/LeadBadgetComponent.vue'));
Vue.component('select-stocks-component', require('./components/common/selects/SelectStocksComponent.vue'));
Vue.component('goods-lister-component', require('./components/leads/GoodsListerComponent'));

// Скидки
Vue.component('discounts-component', require('./components/common/discounts/DiscountsComponent'));

Vue.component('photo-upload-component', require('./components/PhotoUploadComponent.vue'));
Vue.component('metrics-categories-component', require('./components/metrics/categories/MetricsCategoriesComponent'));
Vue.component('plugins-component', require('./components/plugins/PluginsComponent'));

// Компании
Vue.component('director-component', require('./components/companies/director/DirectorComponent'));

// Поиск

Vue.component('search-index-component', require('./components/search/SearchIndexComponent'));

Vue.component('search-city-component', require('./components/search/SearchCityComponent'));
Vue.component('search-leads-component', require('./components/search/SearchLeadsComponent'));
Vue.component('search-clients-component', require('./components/search/SearchClientsComponent'));
Vue.component('search-articles-component', require('./components/search/SearchArticlesComponent'));
Vue.component('search-processes-component', require('./components/search/SearchProcessesComponent'));
Vue.component('search-prices-goods-component', require('./components/search/SearchPricesGoodsComponent'));

Vue.component('search-client-component', require('./components/search/SearchClientComponent'));

Vue.component('sites-component', require('./components/promotions/SitesComponent'));

// Продвижения
Vue.component('promotion-component', require('./components/promotions/PromotionComponent'));
Vue.component('promotion-catalog-goods-component', require('./components/promotions/CatalogGoodsComponent'));

Vue.component('settings-stocks-component', require('./components/companies/settings/SettingsStocksComponent'));

// Vue.component('price-goods-price-component', require('./components/prices_goods/PriceGoodsPriceComponent.vue'));

Vue.component('pickmeup-component', require('./components/inputs/PickmeupComponent'));

// Виджеты
Vue.component('clients-indicators-component', require('./components/widgets/ClientsIndicatorsComponent'));

// Прайсы
Vue.component('prices-goods-component', require('./components/products/articles/prices/PricesGoodsComponent'));
Vue.component('prices-goods-discount-component', require('./components/prices_goods/DiscountComponent'));

Vue.component('filial-cities-component', require('./components/filials/CitiesComponent'));


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

window.pickmeup = require('pickmeup');

pickmeup.defaults.locales['ru'] = {
    days: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
    daysShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    daysMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек']
};

Vue.prototype.$pickmeup = window.pickmeup;

// Vuex хранилище
import store from './store/index.js';
// window.store = store;

const app = new Vue({
    el: '#app',
    store: new Vuex.Store(store)
});

// Основные настройки
require('../common/main');

// Наши скрипты
require('./partials/main');
require('./partials/sidebar');
require('./partials/filter');

// window.CKEDITOR_BASEPATH = 'node_modules/ckeditor/';
// require('ckeditor');
