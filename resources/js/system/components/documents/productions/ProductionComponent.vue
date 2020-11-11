<template>

    <table class="table-invoice">
        <thead>
        <tr>
            <th>№</th>
            <th>Тип:</th>
            <th>Наименование позиции:</th>
            <th>Кол-во:</th>
            <th>Ед. изм.:</th>
            <th></th>
        </tr>
        </thead>

        <tbody id="table-raws">

        <productions-item-component
            v-for="(item, index) in itemsList"
            :item="item"
            :index="index"
            :key="item.id"
            :is-conducted="isConducted"
            :items-count="itemsList.length"
            @update="updateItem"
            @remove="deleteItem(index)"
            @open-modal-cancel="openModal"
        ></productions-item-component>

        <tr
            v-if="!isConducted"
            class="tr-add"
        >
            <td>{{ items.length + 1}}</td>
            <td>
                <select
                    v-model="entity_id"
                    name="entity_id"
                    @change="changeEntity"
                >
                    <option
                        v-for="entity in entities"
                        :value="entity.id"
                        :selected="entity.id === selectedEntity"
                    >{{ entity.name }}
                    </option>
                </select>
            </td>
            <td>
                <select-categories-component
                    :select-categories="selectCategories"
                    :select-categories-items="selectCategoriesItems"
                    :change="change"
                    @set-id="setId"
                    @check-change="checkChange"
                ></select-categories-component>
            </td>
            <td>
                <digit-component
                    :value="count"
                    :decimal-place="0"
                    v-model="count"
                    ref="countComponent"
                ></digit-component>
            </td>
            <td>{{ itemUnit }}</td>
            <td>
                <a
                    @click="addItem"
                    class="button tiny"
                    :disabled="isDisabled"
                >Добавить</a>
            </td>
        </tr>

        </tbody>

        <tfoot>
        <tr>
            <td colspan="3">Итого:</td>
            <td></td>
            <td
                v-if="!isConducted"
            ></td>
            <td></td>

        </tr>
        </tfoot>

        <div
            class="reveal rev-small"
            id="modal-cancel"
            data-reveal
        >
            <div class="grid-x">
                <div class="small-12 cell modal-title">
                    <h5>Отмена производства</h5>
                </div>
            </div>
            <div class="grid-x align-center modal-content">
                <div class="small-10 cell text-center">
                    <p>Отменяем "{{ cancelingItemName }}", вы уверены?</p>
                </div>
            </div>
            <div class="grid-x align-center grid-padding-x">
                <div class="small-6 medium-4 cell">
                    <form
                        method="POST"
                        :action="'/admin/productions_items/cancel/' + cancelingItemId"
                    >
                        <slot></slot>
                        <button
                             class="button modal-button"
                            type="submit"
                        >Откатить
                        </button>
                    </form>
                </div>
                <div class="small-6 medium-4 cell">
                    <button data-close class="button modal-button" type="submit">Отменить</button>
                </div>
            </div>
        </div>
    </table>

</template>

<script>
    export default {
        components: {
            'productions-item-component': require('./ProductionsItemComponent'),
            'select-categories-component': require('../../common/selects_categories/SelectCategoriesComponent'),
            'digit-component': require('../../inputs/DigitComponent'),
        },
        props: {
            production: Object,
            selectData: Object
        },
        data() {
            return {
                // Сущности
                entities: this.selectData.entities,
                selectedEntity: this.selectData.entities[0].id,
                entity_id: this.selectData.entities[0].id,

                //
                items: this.production.items,
                id: null,
                count: 0,

                // Категории для компонента выбора
                categories: this.selectData.categories,
                categoriesItems: this.selectData.items,
                change: false,
                itemUnit: null,

                // Производитель
                manufacturer_id: null,

                cancelingItemId: null,
                cancelingItemName: null,
            }
        },
        computed: {
            isDisabled() {
                return this.id == null || this.count == 0;
            },
            itemsList() {
                return this.items;
            },
            totalItemsCount() {
                return this.items.length;
            },

            // Списки для компонента выбора
            selectCategories() {
                return this.categories.filter(item => {
                    return item.entity_id === this.entity_id
                })
            },
            selectCategoriesItems() {
                return this.categoriesItems.filter(item => {
                    return item.entity_id === this.entity_id
                })
            },
            isConducted() {
                return this.production.conducted_at;
            },
        },

        methods: {
            changeCount(value) {
                this.count = value;
            },
            changeEntity() {
                this.change = true;

                let count = 0;
                this.categories.filter(item => {
                    if (item.entity_id === this.entity_id) {
                        count++
                    }
                });

                if (count === 0) {
                    axios
                        .post('/admin/productions/categories', {
                            entity_id: this.entity_id,
                        })
                        .then(response => {
                            this.categories = this.categories.concat(response.data.categories);
                            this.categoriesItems = this.categoriesItems.concat(response.data.items);
                        })
                        .catch(error => {
                            console.log(error);
                        })
                }

            },
            checkChange: function () {
                this.change = false;
            },
            setId: function (id) {
                this.id = id;
                if (id != null) {
                    this.categoriesItems.filter(item => {
                        if (item.id === id && item.entity_id === this.entity_id) {
                            if (item.article.package_status === 1) {
                                this.itemUnit = item.article.package_abbreviation;
                            } else {
                                this.itemUnit = item.article.unit.abbreviation;
                            }

                            this.manufacturer_id = item.article.manufacturer_id;
                        }
                    });
                } else {
                    this.itemUnit = null;
                    this.manufacturer_id = null;
                }
            },

            addItem: function () {
                if (!this.isDisabled) {
                    this.disabledButton = true;
                    axios
                        .post('/admin/productions_items', {
                            production_id: this.production.id,
                            cmv_id: this.id,
                            entity_id: this.entity_id,
                            count: this.count,
                            manufacturer_id: this.manufacturer_id,
                        })
                        .then(response => {
                            this.items.push(response.data);

                            this.id = null;
                            this.change = true;
                            this.manufacturer_id = null;

                            this.count = 0;
                            this.$refs.countComponent.update(this.count);
                        })
                        .catch(error => {
                            console.log(error)
                        });
                }
            },
            updateItem: function (item, index) {
                Vue.set(this.items, index, item);
            },
            deleteItem: function (index) {
                this.items.splice(index, 1);
            },

            openModal(item) {
                this.cancelingItemId = item.id;
                this.cancelingItemName = item.cmv.article.name;
            },
        },

        filters: {
            roundToTwo: function (value) {
                return Math.trunc(parseFloat(Number(value).toFixed(2)) * 100) / 100;
            },

            // Создает разделители разрядов в строке с числами
            level: function (value) {
                return Number(value).toLocaleString();
            },
        },

    }
</script>
