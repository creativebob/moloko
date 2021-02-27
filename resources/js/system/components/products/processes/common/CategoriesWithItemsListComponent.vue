<template>
    <div class="grid-x grid-margin-x">
        <div class="cell small-10">
            <label class="input-icon">
                <input
                    type="text"
                    v-model="text"
                    @input="reset"
                    maxlength="30"
                    autocomplete="off"
                    @keydown.enter.prevent="onEnter"
                >

                <div
                    class="sprite-input-right"
                    :class="status"
                    @click="clear"
                >
                </div>
            </label>
            <table class="content-table-search table-over">
                <tbody>

                <template v-if=search>
                    <tr v-for="item in results">
                        <td>
                            <span v-if="item.add == true">{{ item.article.name }} (добавлен)</span>
                            <a
                                v-else
                                @click="addFromSearch(item.id)"
                            >{{ item.article.name }}</a>
                        </td>
                    </tr>
                </template>

                <tr v-if=error class="no-city">
                    <td>Ничего не найдено</td>
                </tr>

                </tbody>
            </table>
        </div>

        <div class="cell small-2">
            <div class="text-center">
                <div
                    class="sprite-input-right sprite-16 icon-select"
                    :data-toggle="'dropdown-' + name"
                    @click="clear"
                ></div>
            </div>
        </div>
        <div
            class="dropdown-pane"
            :id="'dropdown-' + name"
            data-dropdown
            data-position="bottom"
            data-alignment="center"
            data-close-on-click="true"
        >

            <ul class="checker" id="categories-list">

                <template v-if="categories.length">
                    <li
                        v-for="category in categories"
                    >
                        <span
                            class="parent"
                            :data-open="name + '_category-' + category.id"
                            @click="openItemItems(name + '_category-' + category.id)"
                        >{{ category.name }}</span>
                        <div
                            class="checker-nested"
                            :id="name + '_category-' + category.id"

                        >
                            <ul class="checker">

                                <li
                                    v-for="item in activeItems(category.id)"
                                    class="checkbox"
                                >
                                    <checkbox-component
                                        :item="item"
                                        :name="name"
                                        @add="addItem"
                                        @remove="removeItem"
                                        :start-checked="checked(item.id)"
                                    ></checkbox-component>
                                </li>

                            </ul>
                        </div>
                    </li>
                </template>
                <li v-else>Ничего нет...</li>
            </ul>

        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'checkbox-component': require('./CheckboxComponent.vue'),
        },
        props: {
            categories: Array,
            items: Array,
            actualItems: Array,
            name: String
        },
        data() {
            return {
                curItems: this.actualItems,
                text: null,
                search: false,
                error: false,
                results: [],
            }
        },
        computed: {
            status() {
                let result;
                if (this.text) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            },

        },
        methods: {
            activeItems(categoryId) {
                return this.items.filter(item => item.category_id === categoryId);
            },
            check() {
                if (this.text.length >= 2) {
                    this.results = this.items.filter(item => {
                        return item.article.name.toLowerCase().includes(this.text.toLowerCase());
                    });
                }

                this.search = (this.results.length > 0);
                if (this.search) {
                    var $vm = this;
                    this.results.forEach(searchItem => {
                        // console.log($vm.curItems);
                        let found = $vm.curItems.find(item => item.id == searchItem.id);
                        if (found) {
                            searchItem.add = true;
                        } else {
                            searchItem.add = false;
                        }
                    });
                } else {

                }

                this.error = (this.results.length == 0);

                // if (this.search) {
                // this.showCategories = false;
                // }
            },
            reset() {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.id = null;
                this.error = false;
                this.search = false;
                this.results = [];

                if (this.text && this.text.length > 2) {
                    this.check();
                }
            },
            clear() {
                this.text = null;
                this.reset();
            },
            addFromSearch(id) {
                // console.log('Клик по пришедшим данным, добавляем в инпут');
                this.text = null;
                this.error = false;
                this.search = false;
                this.results = [];
                // this.showCategories = false;

                let find = this.curItems.find(item => item.id == id);
                if (! find) {
                    this.$emit('add', this.items.find(item => item.id == id));
                }

            },
            onEnter() {
                if (this.results.length === 1) {
                    this.addFromSearch(this.results[0].id);
                }
            },
            checked(id) {
                if (this.curItems.length) {
                    let find = this.actualItems.find(item => item.id == id);
                    if (find) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false
                }
            },
            openItemItems(id) {
                // Скрываем все состав
                $('.checker-nested').hide();
                // Показываем нужную
                $('#' + id).show();
            },
            addItem(item) {
                this.$emit('add', item);
            },
            removeItem(id) {
                this.$emit('remove', id);
            },
        }
    }
</script>
