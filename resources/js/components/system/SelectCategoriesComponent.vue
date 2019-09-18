<template>
    <div>
        <label id="" class="input-icon">
            <input
                type="text"
                v-model="text"
                @input="reset"
                maxlength="30"
                autocomplete="off"
                pattern="[А-Яа-яЁё0-9-_\s]{3,30}"
                v-on:keydown.enter.prevent="onEnter"
            >

            <div
                    class="sprite-input-right sprite-16 icon-select"
                    @click='showCategories = !showCategories'
            >
            </div>
            <span class="form-error">Уж постарайтесь, введите город!</span>

        </label>

        <input
                type="hidden"
                v-model="id"
                maxlength="3"
                pattern="[0-9]{3}"
        >

        <div
                v-if="showCategories"
                class="relat"
        >
            <ul

                class="vertical menu abs"
                data-drilldown
                data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'
            >

            <li
                    v-for="category in categories"
                    class="item-catalog"
            >
                <a class="get-prices">{{ category.name }}</a>


                <ul
                        v-if="category.childrens"
                        class="menu vertical nested"
                >
                    <li
                            v-for="children in category.childrens"
                            class="item-catalog"
                    >
                        <a class="get-prices">{{ children.name }}</a>
                    </li>

                </ul>


            </li>


        </ul>
        </div>

        <table class="content-table-search table-over">
            <tbody>

                <template v-if=search>
                    <tr v-for="(item, index) in results">
                        <td>
                            <a @click="add(index)">{{ item.name }}</a>
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
        mounted() {

            axios.get('/api/v1/categories_select/' + this.entity)
                .then(response => {
                    this.categories = response.data.categories
                    this.items = response.data.items
            })
                .catch(error => {
                    console.log(error)
                })
        },
        props: {
            entity: {
                type: String,
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
            },
            add(index) {
                // console.log('Клик по пришедшим данным, добавляем в инпут');
                this.id = this.results[index].id;
                this.text = this.results[index].name;
                this.found = true;
                this.error = false;
                this.search = false;
                this.results = [];
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
                    this.add(0);
                }
            }
        }
    }
</script>
