/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../common/bootstrap');

// window.Vue = require('vue');
// window.Vuex = require('vuex');

import Vue from 'vue';
import Vuex from 'vuex';
window.Vue = Vue;
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


Vue.component('consignment-component', require('./components/consignments/ConsignmentComponent.vue'));
Vue.component('production-component', require('./components/productions/ProductionComponent.vue'));

Vue.component('estimate-component', require('./components/estimates/EstimateComponent.vue'));
Vue.component('estimate-register-button-component', require('./components/estimates/EstimateRegisterButtonComponent.vue'));
Vue.component('estimate-sale-button-component', require('./components/estimates/EstimateSaleButtonComponent.vue'));
Vue.component('estimate-production-button-component', require('./components/estimates/EstimateProductionButtonComponent.vue'));
Vue.component('payments-component', require('./components/PaymentsComponent.vue'));

Vue.component('input-digit-component', require('./components/InputDigitComponent.vue'));
Vue.component('dropzone-component', require('./components/DropzoneComponent.vue'));

// ТМЦ
Vue.component('articles-categories-with-groups-component', require('./components/ArticlesCategoriesWithGroupsComponent.vue'));
Vue.component('manufacturers-component', require('./components/MAnufacturersComponent.vue'));

// Услуги
Vue.component('processes-categories-with-groups-component', require('./components/ProcessesCategoriesWithGroupsComponent.vue'));

Vue.component('catalog-goods-component', require('./components/catalogs/goods/CatalogGoodsComponent.vue'));
Vue.component('catalog-services-component', require('./components/catalogs/services/CatalogServicesComponent.vue'));
Vue.component('lead-badget-component', require('./components/LeadBadgetComponent.vue'));
Vue.component('select-stocks-component', require('./components/common/selects/SelectStocksComponent.vue'));

Vue.component('photo-upload-component', require('./components/PhotoUploadComponent.vue'));
Vue.component('metrics-categories-component', require('./components/metrics/categories/MetricsCategoriesComponent'));
Vue.component('plugins-component', require('./components/plugins/PluginsComponent'));

// Поиск
Vue.component('search-city-component', require('./components/search/SearchCityComponent'));
Vue.component('search-lead-component', require('./components/search/SearchLeadComponent'));
Vue.component('search-articles-component', require('./components/search/SearchArticlesComponent'));
Vue.component('search-processes-component', require('./components/search/SearchProcessesComponent'));
Vue.component('search-prices-goods-component', require('./components/search/SearchPricesGoodsComponent'));

Vue.component('sites-component', require('./components/promotions/SitesComponent'));
Vue.component('promotion-catalog-goods-component', require('./components/promotions/CatalogGoodsComponent'));

Vue.component('settings-stocks-component', require('./components/settings/SettingsStocksComponent'));

// Vue.component('price-goods-price-component', require('./components/prices_goods/PriceGoodsPriceComponent.vue'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
// Vuex хранилище
import store from './store/index.js';

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
