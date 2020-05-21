<template>
    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-9 cell">
            <table class="table-compositions">

                <thead>
                <tr>
                    <th>Категория</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Ед. изм.</th>
                    <th></th>
                </tr>
                </thead>

                <tbody id="table-related">

                    <template v-if="relatedItems.length">
                        <tr
                            v-for="relatedItem in relatedItems"
                            class="item"
                            :id="'table-related-' + relatedItem.id"
                            :data-name="relatedItem.article.name"
                        >
                            <td>{{ relatedItem.category.name }}</td>
                            <td>{{ relatedItem.article.name }} <span v-if="relatedItem.article.draft == 1" class="mark-draft">Черновик</span></td>
                            <td>{{ relatedItem.article.description }}</td>
                            <td>{{ relatedItem.article.unit.abbreviation }}</td>
                            <td class="td-delete">
                                <a class="icon-delete sprite"
                                   @click="deleteRelated(relatedItem.id)"
                                ></a>
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>

        <div class="small-12 medium-3 cell">
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
                                    <span v-if="item.add">{{ item.article.name }} (добавлен)</span>
                                    <a
                                        v-else
                                        @click="addFromSearch(item.id)"
                                    >{{ item.article.name }}</a>
                                </td>
                            </tr>
                        </template>

                        <tr v-if=error class="no-city">
                            <td>Товар не найден</td>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <div class="cell small-2">
                    <div class="text-center">
                        <div
                            class="sprite-input-right sprite-16 icon-select"
                            data-toggle="dropdown-related"
                        ></div>
                    </div>
                </div>
            </div>
            <div class="dropdown-pane" id="dropdown-related" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

                <ul class="checker" id="categories-list">

                    <template v-if="relatedCategories.length">
                        <li
                            v-for="relatedCategory in relatedCategories"
                        >
                            <span class="parent" :data-open="'related_category-' + relatedCategory.id">{{ relatedCategory.name }}</span>
                            <div class="checker-nested" :id="'related_category-' + relatedCategory.id">
                                <ul class="checker">

                                    <li
                                        v-for="curGoods in activeRelatedGoods(relatedCategory.id)"
                                        class="checkbox"
                                    >
                                        <checkbox-related-component
                                            :cur-goods="curGoods"
                                            @add="addRelated"
                                            @del="deleteRelated"
                                            :start-checked="checked(curGoods.id)"
                                        ></checkbox-related-component>
                                    </li>

                                </ul>
                            </div>
                        </li>
                    </template>
                    <li v-else>Ничего нет...</li>
                </ul>

            </div>
        </div>
    </div>

</template>

<script>
    export default {
        components: {
            'checkbox-related-component': require('./CheckboxRelatedComponent.vue'),
        },
        props: {
            relatedCategories: Array,
            relatedGoods: Array,
            related: Array,
        },
        data() {
            return {
                relatedItems: this.related,
                text: null,
                search: false,
                error: false,
                results: [],
            }
        },
        computed: {
            status() {
                let result;
                if (this.error) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            }
        },
        methods: {
            activeRelatedGoods(categoryId) {
                return this.relatedGoods.filter(related => related.category_id === categoryId);
            },
            check() {
                if (this.text.length >= 2) {
                    this.results = this.relatedGoods.filter(item => {
                        return item.article.name.toLowerCase().includes(this.text.toLowerCase());
                    });
                }

                this.search = (this.results.length > 0);
                if (this.search) {
                    var relatedItems = this.relatedItems;
                    this.results.forEach(function(item) {
                        relatedItems.find(related => {
                            if (item.id == related.id) {
                                item.add = true;
                            }
                        })
                    });
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

                if (this.text.length > 2) {
                    this.check();
                }
            },
            clear() {
                if (this.error) {
                    this.text = null;
                    this.reset();
                }
            },
            addFromSearch(id) {
                // console.log('Клик по пришедшим данным, добавляем в инпут');
                this.text = null;
                this.error = false;
                this.search = false;
                this.results = [];
                // this.showCategories = false;

                let find = this.relatedItems.find(item => item.id == id);
                if (! find) {
                    this.relatedItems.push(this.relatedGoods.find(item => item.id == id));
                }

            },
            onEnter() {
                if (this.results.length === 1) {
                    this.addFromSearch(this.results[0].id);
                }
            },
            checked(id) {
                if (this.relatedItems.length) {
                    let find = this.relatedItems.find(item => item.id == id);
                    if (find) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false
                }
            },
            addRelated(id) {
                var itemId = id,
                    $vm = this;

                this.relatedGoods.find(related => {
                    if (related.id == itemId) {
                        $vm.relatedItems.push(related);
                    }
                });
            },
            deleteRelated(id) {
                let index = this.relatedItems.findIndex(item => item.id == id);
                this.relatedItems.splice(index, 1);
            }
        }
    }
</script>
