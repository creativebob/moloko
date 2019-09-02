/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('citysearch-component', require('../components/system/CitySearchComponent.vue'));
Vue.component('consignmentitemadd-component', require('../components/system/ConsignmentItemAddComponent.vue'));
Vue.component('input-digit-component', require('../components/system/InputDigitComponent.vue'));
Vue.component('dropzone-component', require('../components/system/DropzoneComponent.vue'));
Vue.component('rawcomposition-component', require('../components/system/RawCOmpositionComponent.vue'));


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});

// Основные настройки
require('../main');

// Наши скрипты
require('./partials/main');
require('./partials/filter');
require('./partials/sidebar');
