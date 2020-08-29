<template>
    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-9 cell">

            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-6 large-9 cell cmv-indicators">
                    <div class="grid-x grid-margin-x">
                        <div class="cell shrink">
                            <span class="indicator-name">Вес: </span><span class="indicators_total">{{ totalWeight }}</span> <span>гр.</span>
                        </div>
                        <div class="cell auto">
                            <span class="indicator-name">Себестоимость: </span><span class="indicators_total">{{ totalCost }}</span> <span>руб.</span>
                        </div>
                    </div>

<!--                    <p>Использовать фото упаковки вместо фото товара?</p><br>-->
<!--                    <div class="cell small-12 switch tiny">-->
<!--                        <input class="switch-input" id="yes-no" type="checkbox" name="exampleSwitch">-->
<!--                        <label class="switch-paddle" for="yes-no">-->
<!--                            <span class="show-for-sr">Использовать фото упаковки вместо фото товара?</span>-->
<!--                            <span class="switch-active" aria-hidden="true"> Да</span>-->
<!--                            <span class="switch-inactive" aria-hidden="true">Нет</span>-->
<!--                        </label>-->
<!--                    </div>-->

                </div>

                <div v-if="! disabled" class="small-12 medium-6 large-3 cell">
                    <categories-list-component
                        :categories="categories"
                        :items="items"
                        :actual-items="actualItems"
                        :name="name"
                        @add="addItem"
                        @remove="removeItem"
                        ref="categoriesListComponent"
                    ></categories-list-component>
                </div>

            </div>

            <div class="grid-x grid-padding-x">
                <div class="small-12 cell">
                    <table class="table-compositions">

                        <thead>
                        <tr>
                            <th>п/п</th>
                            <th>Категория:</th>
                            <th>Продукт:</th>
                            <th>Кол-во:</th>
                            <th>Использование:</th>
<!--                            <th>Отход:</th>-->
<!--                            <th>Остаток:</th>-->
<!--                            <th>Операция над остатком:</th>-->
                            <th>Вес</th>
                            <th>Себестоимость</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody
                            :id="'table-' + name"
                        >

                        <template v-if="actualItems && actualItems.length">
                            <composition-component
                                v-for="(item, index) in actualItems"
                                :item="item"
                                :index="index"
                                :name="name"
                                :key="item.id"
                                @open-modal="openModal"
                                :disabled="disabled"
                                @update="updateItem"
                            ></composition-component>
                        </template>

                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="5"></td>
                            <td>
                                <span>{{ totalWeight }}</span> <span>гр.</span>
                            </td>
                            <td>
                                <span>{{ totalCost }}</span> <span>руб.</span>
                            </td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <modal-remove-component
                :item="removingItem"
                :name="name"
                @remove="removeItem"
            ></modal-remove-component>

        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'composition-component': require('./CompositionComponent'),
            'modal-remove-component': require('./ModalRemoveComponent'),
            'categories-list-component': require('../common/CategoriesWithItemsListComponent'),
        },
        props: {
            categories: Array,
            items: Array,
            itemItems: Array,
            name: String,
            disabled: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                curItems: this.itemItems,
                text: null,
                search: false,
                error: false,
                results: [],
                removingItem: {
                    article: {
                        name: null
                    }
                }
            }
        },
        mounted() {
            // let composition = {
            //         name: this.name,
            //         items: this.actualItems
            //     };
            this.updateStore();
        },
        computed: {
            actualItems() {
                return this.curItems;
            },
            totalWeight() {
                var weight = 0;
                this.curItems.forEach(item => {
                    if (item.pivot) {
                        weight = parseFloat(weight) + (parseFloat(item.weight) * 1000 *  parseFloat(item.pivot.useful));
                    }
                });
                return weight.toFixed(2);
            },
            totalCost() {
                var cost = 0;
                if (this.name == 'attachments' || this.name == 'containers') {
                    this.curItems.forEach(item => {
                        if (item.pivot) {
                            cost = parseFloat(cost) + (parseFloat(item.cost_unit) * parseFloat(item.pivot.useful));
                        }
                    });
                } else if (this.name == 'raws') {
                    this.curItems.forEach(item => {
                        if (item.pivot) {
                            cost = parseFloat(cost) + (parseFloat(item.cost_portion) * parseFloat(item.pivot.useful));
                        }
                    });
                }
                return cost.toFixed(2);
            },
            composition() {
                return {
                    name: this.name,
                    items: this.actualItems
                };
            }

        },
        methods: {
            addItem(item) {
                this.curItems.push(item);
                this.updateStore();
            },
            updateItem(item){
                let found = this.curItems.find(obj => obj.id == item.id);
                Vue.set(found, 'item', item);
                this.updateStore();
            },
            removeItem(id) {
                let index = this.curItems.findIndex(item => item.id == id);
                this.curItems.splice(index, 1);
                this.$refs.categoriesListComponent.clear();
                this.updateStore();
            },
            openModal(item) {
                this.removingItem = item;
            },
            updateStore() {
                this.$store.commit('SET_COMPOSITION', this.composition);
            }
        }
    }
</script>
