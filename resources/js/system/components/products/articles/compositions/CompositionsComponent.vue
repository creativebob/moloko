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
                },
                totalWeight: 0,
                totalCost: 0
            }
        },
        created() {
            var totalWeight = 0,
                totalCost = 0;

            this.curItems.forEach(item => {
                let weight = parseFloat(item.weight * 1000 * item.pivot.useful).toFixed(2);
                totalWeight = parseFloat(totalWeight) + parseFloat(weight);
                item.totalWeight = weight;

                let cost = 0;
                if (this.name == 'attachments' || this.name == 'containers') {
                    cost = parseFloat(item.cost_unit * item.pivot.useful).toFixed(2);
                } else if (this.name == 'raws') {
                    cost = parseFloat(item.cost_portion * item.pivot.useful).toFixed(2);
                }

                totalCost = parseFloat(totalCost) + parseFloat(cost);
                item.totalCost = cost;
            });

            this.totalWeight = totalWeight.toFixed(2);
            this.totalCost = totalCost.toFixed(2);

            this.updateStore();
        },
        computed: {
            actualItems() {
                return this.curItems;
            },
            composition() {
                return {
                    name: this.name,
                    items: this.actualItems
                };
            }
        },
        methods: {
            setTotalWeight() {
                var weight = 0;
                this.curItems.forEach(item => {
                    weight += parseFloat(item.totalWeight);
                });
                this.totalWeight = weight.toFixed(2);
            },
            setTotalCost() {
                var cost = 0;
                this.curItems.forEach(item => {
                    cost += parseFloat(item.totalCost);
                });
                this.totalCost = cost.toFixed(2);
            },
            addItem(item) {
                item.pivot = {
                    value: 0,
                    useful: 0
                };
                item.totalWeight = parseFloat('0').toFixed(2);
                item.totalCost = parseFloat('0').toFixed(2);

                this.curItems.push(item);
                this.setTotalWeight();
                this.setTotalCost();
                this.updateStore();
            },
            updateItem(item){
                let found = this.curItems.find(obj => obj.id == item.id);
                Vue.set(found, 'item', item);
                this.setTotalWeight();
                this.setTotalCost();
                this.updateStore();
            },
            removeItem(id) {
                let index = this.curItems.findIndex(item => item.id == id);
                this.curItems.splice(index, 1);
                this.$refs.categoriesListComponent.clear();
                this.setTotalWeight();
                this.setTotalCost();
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
