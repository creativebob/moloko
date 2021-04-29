/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../common/foundation');

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


// Корзина
Vue.component('cart-header-component', require('./components/cart/HeaderComponent.vue'));
Vue.component('cart-component', require('./components/cart/CartComponent.vue'));
Vue.component('cart-form-component', require('./components/cart/CartFormComponent'));

// Товары
Vue.component('prices-goods-component', require('./components/prices/goods/PricesGoodsComponent'));
Vue.component('search-component', require('./components/prices/goods/SearchComponent'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vuex хранилище
import store from './store/index';

const app = new Vue({
    el: '#app',
    store: new Vuex.Store(store)
});

// Включаем пакет слайдера Slick
import 'slick-carousel';

// Основные настройки
$(document).foundation();
