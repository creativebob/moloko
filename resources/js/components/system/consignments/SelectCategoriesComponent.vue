<template>
    <div>
        <label id="" class="input-icon">
            <input
                type="text"
                v-model="text"
                @input="reset"
                maxlength="30"
                autocomplete="off"
                @keydown.enter.prevent="onEnter"
            >

            <div
                    class="sprite-input-right sprite-16 icon-select"
                    @click="toggleShowCategories()"
            >
            </div>

        </label>

        <input
                type="hidden"
                v-model="id"
                maxlength="3"
                pattern="[0-9]{3}"
                @change="setId(id)"
        >

        <div
                v-if="!hideCategories && showCategories"
                class="drilldown-categories-wrap"
        >
            <div class="categories-wrap">
                <ul

                    class="vertical menu"
                    v-drilldown
                    data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'
                >

                <li
                        v-for="category in selectCategories"
                        class="item-catalog"
                >
                    <a
                        @click="getItems(category.id)"
                    >{{ category.name }}</a>


                    <ul
                            v-if="category.childrens && category.childrens.length"
                            class="menu vertical nested"
                    >
                        <childrens-component v-for="children in category.childrens" :category="children" :key="children.id"></childrens-component>

                    </ul>

                </li>

            </ul>

                <ul v-if="listItems.length > 0" class="vertical menu">
                    <li v-for="item in listItems">
                        <a @click="addFromList(item.id)">{{ item.name }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <table class="content-table-search table-over">
            <tbody>

                <template v-if=search>
                    <tr v-for="(item, index) in results">
                        <td>
                            <a @click="addFromSearch(index)">{{ item.name }}</a>
                        </td>
                    </tr>
                </template>

                <tr v-if=error class="no-city">
                    <td>Населенный пункт не найден в базе данных, <a href="/admin/cities" target="_blank">добавьте его!</a></td>
                </tr>

            </tbody>
        </table>
     </div>

</template>

<script>
    export default {
        components: {
            'childrens-component': require('./SelectCategoriesChildrensComponent.vue')
        },
        name: 'select-categories-component',
        props: {
            hide: Boolean,
            selectCategories: Array,
            selectCategoriesItems: Array
        },
        data() {
            return {
                id: null,
                text: null,
                search: false,
                found: false,
                error: false,
                listItems: [],
                results: [],
                showCategories: !this.hide,

            };
        },
        computed: {
            hideCategories() {
                if (this.hide) {
                    this.showCategories = false;
                    this.listItems = [];
                    this.clear();
                } else {
                    return this.hide;
                }
            },
        },
        methods: {
            check() {
                // console.log('Ищем введеные данные в наших городах (подгруженных), затем от результата меняем состояние на поиск или ошибку');

                this.results = this.selectCategoriesItems.filter(item => {
                    return item.name.toLowerCase().includes(this.text.toLowerCase());
                });

                this.search = (this.results.length > 0)

                if (this.search) {
                    this.hide = true;
                    this.showCategories = false;
                }
            },
            toggleShowCategories() {
                this.$parent.checkHide();

                this.showCategories = !this.showCategories
                if (!this.showCategories) {
                    this.listItems = []
                }
            },
            addFromSearch(index) {
                // console.log('Клик по пришедшим данным, добавляем в инпут');
                this.id = this.results[index].id;
                this.text = this.results[index].name;
                this.found = true;
                this.error = false;
                this.search = false;
                this.results = [];
                this.hide = true;
                this.showCategories = false;
                this.resetSelect = false;

                this.setId();
            },
            addFromList(id) {

                let it = this.selectCategoriesItems.filter(item => {
                    return item.id == id;
                })
                // console.log('Клик по пришедшим данным, добавляем в инпут');
                this.id = it[0].id;
                this.text = it[0].name;
                this.found = true;
                this.error = false;
                this.search = false;
                this.results = [];
                this.listItems = [];
                this.hide = true;
                this.showCategories = false;

                this.setId();
            },
            clear() {
                if (this.error) {
                    // console.log('Клик по иконке ошибки на инпуте, обнуляем');
                    this.text = '';
                    this.id = null;
                    this.found = false;
                    this.error = false;
                    this.results = [];
                    this.hide = true;
                    this.showCategories = false;

                    this.setId();
                }
            },
            reset() {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.id = null;
                this.found = false;
                this.error = false;
                this.search = false;
                this.results = [];

                this.setId();

                if (this.text.length > 0) {
                    this.check();
                }
            },
            onEnter() {
                if (this.results.length == 1) {
                    this.addFromSearch(0);
                }
            },
            getItems(id) {
                this.listItems = this.selectCategoriesItems.filter(item => {
                    return item.category_id == id;
                });

                this.id = null;
                this.name = '';

                this.setId();
            },
            setId: function () {
                this.$parent.setId(this.id);
            },
        },
        directives: {
            'drilldown': {
                bind: function (el) {
                    new Foundation.Drilldown($(el))
                }
            }
        }
    }
</script>
