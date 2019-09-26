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
        >

        <div
                v-if="showCategories"
                class="drilldown-categories-wrap"
        >
            <div class="categories-wrap">
                <ul

                    class="vertical menu"
                    v-drilldown
                    data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'
                >

                <li
                        v-for="category in categories"
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
        mounted() {
            this.categories = this.data.categories
            this.items = this.data.items
        },
        props: {
            data: {
                type: Object,
            }
        },
        data() {
            return {
                id: null,
                text: null,
                search: false,
                found: false,
                error: false,
                items: [],
                listItems: [],
                categories: [],
                results: [],
                showCategories: false

            };
        },
        computed: {
            status() {
                let result;

                if (this.found) {
                    result = 'sprite-16 icon-success'
                }
                if (this.error) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            },
            // filteredItems() {
            //     if (this.text) {
            //         this.search = true
            //         return this.items.filter(item => {
            //             return item.name.toLowerCase().includes(this.text.toLowerCase());
            //         });
            //
            //     }
            //     return this.items
            // }
        },
        methods: {
            check() {
                // console.log('Ищем введеные данные в наших городах (подгруженных), затем от результата меняем состояние на поиск или ошибку');

                this.results = this.items.filter(item => {
                    return item.name.toLowerCase().includes(this.text.toLowerCase());
                });

                this.search = (this.results.length > 0)

                if (this.search) {
                    this.showCategories = false
                }
            },
            toggleShowCategories() {
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
            },
            addFromList(id) {

                let it = this.items.filter(item => {
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
                this.showCategories = false;
            },
            clear() {
                if (this.error) {
                    // console.log('Клик по иконке ошибки на инпуте, обнуляем');
                    this.text = '';
                    this.id = null;
                    this.found = false;
                    this.error = false;
                    this.results = [];
                }
            },
            reset() {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.id = null;
                this.found = false;
                this.error = false;
                this.search = false;
                this.results = [];

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
                this.listItems = this.items.filter(item => {
                    return item.category_id == id;
                });

                this.id = null;
                this.name = '';
            }
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
